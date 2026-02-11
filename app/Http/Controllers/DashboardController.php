<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Loyer;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $statsService;

    public function __construct(\App\Services\DashboardStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        Carbon::setLocale('fr');
        $user = auth()->user();

        // Données communes optimisées (Pagination systématique et Eager Loading)
        $commonData = [
            'biens_list' => Bien::with(['contrats.locataire', 'images', 'imagePrincipale', 'proprietaire'])->latest()->paginate(25, ['*'], 'page_biens'),
            'depenses_list' => \App\Models\Depense::with('bien.proprietaire')->latest()->paginate(25, ['*'], 'page_depenses'),
            'categories_depenses' => ['maintenance', 'travaux', 'taxe', 'assurance', 'autre'],
            'locataires_list' => Locataire::with(['contrats.bien', 'contrats.loyers'])->withCount('contrats')->latest()->paginate(25, ['*'], 'page_locataires'),
            'contrats_list' => Contrat::with(['bien:id,nom,adresse', 'locataire:id,nom,telephone', 'loyers'])->latest()->paginate(25, ['*'], 'page_contrats'),
            'loyers_list' => Loyer::withMontantPaye()->with(['contrat.locataire', 'contrat.bien', 'paiements'])->orderBy('id', 'desc')->paginate(50, ['*'], 'page_loyers'),
            'paiements_list' => Paiement::with(['loyer.contrat.locataire', 'loyer.contrat.bien'])->latest()->paginate(50, ['*'], 'page_paiements'),
            'proprietaires_list' => in_array($user->role, ['admin', 'gestionnaire', 'direction'])
                ? Proprietaire::withCount(['biens as logements_count'])->get()
                : collect([]),
            'alerts' => $this->statsService->getAlerts(),
            'parc_stats' => $this->statsService->getParcStats(),
        ];

        $roleData = match ($user->role) {
            'admin' => $this->getAdminData(),
            'gestionnaire' => $this->getGestionnaireData(),
            'comptable' => $this->getComptableData(),
            'direction' => $this->getDirectionData(),
            'proprietaire' => $this->getProprietaireData(),
            default => abort(403, 'Rôle non autorisé'),
        };

        $data = array_merge($commonData, $roleData);

        return view('dashboard.index', compact('data'));
    }

    private function getAdminData()
    {
        return [
            'role' => 'admin',
            'users_list' => User::all(),
            'logs_list' => ActivityLog::with('user')->latest()->limit(50)->get(),
            'financial_stats' => $this->statsService->getFinancialKPIs(),
            'chart_data' => $this->statsService->getChartData(),
        ];
    }

    private function getGestionnaireData()
    {
        $financial = $this->statsService->getFinancialKPIs();
        $parc = $this->statsService->getParcStats();

        return [
            'role' => 'gestionnaire',
            'kpis' => [
                'total_logements' => $parc['total_biens'],
                'logements_libres' => $parc['biens_vacants'],
                'contrats_actifs' => $parc['biens_occupes'],
                'loyers_emis_mois' => $financial['loyers_factures'],
                'loyers_payes_mois' => $financial['loyers_encaisses'],
                'total_en_retard' => $financial['arrieres_total'],
            ],
            'derniers_contrats' => Contrat::with(['bien', 'locataire'])->where('statut', 'actif')->latest()->limit(5)->get(),
            'chart_data' => $this->statsService->getChartData(),
        ];
    }

    private function getComptableData()
    {
        $financial = $this->statsService->getFinancialKPIs();

        return [
            'role' => 'comptable',
            'kpis' => [
                'loyers_emis' => $financial['loyers_factures'],
                'loyers_payes' => $financial['loyers_encaisses'],
                'total_impaye' => $financial['arrieres_total'],
                'taux_recouvrement' => $financial['taux_recouvrement'],
            ],
            'loyers_en_attente' => Loyer::with(['contrat.bien', 'contrat.locataire'])->whereIn('statut', ['émis', 'en_retard'])->latest()->limit(10)->get(),
            'derniers_paiements' => Paiement::with(['loyer.contrat.locataire'])->latest('date_paiement')->limit(10)->get(),
            'chart_data' => $this->statsService->getChartData(),
        ];
    }

    private function getProprietaireData()
    {
        $user = auth()->user();
        $proprietaire = Proprietaire::where('email', $user->email)->first();

        if (! $proprietaire) {
            return [
                'role' => 'proprietaire',
                'kpis' => ['revenus' => 0, 'charges' => 0, 'net' => 0],
                'biens_performance' => [],
            ];
        }

        $stats = $this->statsService->getProprietaireStats($proprietaire);

        return [
            'role' => 'proprietaire',
            'kpis' => [
                'revenu_mensuel' => $stats['revenu_mensuel'],
                'charges_mensuelles' => $stats['charges_mensuelles'],
                'commissions_mensuelles' => $stats['commissions_mensuelles'],
                'net_mensuel' => $stats['net_mensuel'],
                'taux_rentabilite' => 0,
            ],
            'biens_list' => $stats['biens_performance'],
            'revenus_par_mois' => $stats['chart_data'],
        ];
    }

    private function getDirectionData()
    {
        $financial = $this->statsService->getFinancialKPIs();
        $parc = $this->statsService->getParcStats();
        $commissionRate = (float) config('real_estate.commission.rate', 0.10);

        return [
            'role' => 'direction',
            'kpis' => [
                'total_logements' => $parc['total_biens'],
                'biens_occupes' => $parc['biens_occupes'],
                'taux_occupation' => $parc['taux_occupation'],
                'revenu_mensuel' => $financial['loyers_encaisses'],
                'taux_collecte' => $financial['taux_recouvrement'],
                'commission_mensuelle' => round($financial['loyers_encaisses'] * $commissionRate),
                'impayes' => $financial['arrieres_total'],
                'valeur_portefeuille' => $financial['gross_potential_rent'],
                'loyer_moyen' => $parc['total_biens'] > 0 ? round($financial['gross_potential_rent'] / $parc['total_biens']) : 0,
                'projection_annuelle' => round($financial['loyers_encaisses'] * $commissionRate) * 12,
                'taux_vacance_economique' => $financial['economic_vacancy_rate'],
                'perte_vacance_economique' => $financial['economic_vacancy_loss'],
            ],
            'repartition_type' => DB::table('biens')->select('type', DB::raw('count(*) as total'))->groupBy('type')->get(),
            'revenus_par_mois' => collect($chartData['labels'])->map(function ($label, $index) use ($chartData) {
                return [
                    'mois' => $label,
                    'montant' => $chartData['encaissements'][$index] ?? 0,
                ];
            })->all(),
            'derniers_paiements' => Paiement::with(['loyer.contrat.locataire'])->latest()->limit(5)->get(),
            'contrats_expiration' => Contrat::with(['bien', 'locataire'])
                ->where('statut', 'actif')
                ->whereNotNull('date_fin')
                ->where('date_fin', '<=', Carbon::now()->addDays(60))
                ->orderBy('date_fin', 'asc')
                ->get(),
        ];
    }

    /**
     * Exporter le rapport mensuel en PDF
     */
    public function exporterRapportMensuel($mois = null)
    {
        $mois = $mois ?? request('mois') ?? Carbon::now()->format('Y-m');
        $commissionRate = (float) config('real_estate.commission.rate', 0.10);

        $financial = $this->statsService->getFinancialKPIs($mois);
        $parc = $this->statsService->getParcStats();
        $chart = $this->statsService->getChartData(6);

        $data = [
            'biens_list' => Bien::with('proprietaire')->latest()->get(),
            'kpis' => [
                'revenu_mensuel' => $financial['loyers_encaisses'],
                'taux_occupation' => $parc['taux_occupation'],
                'impayes' => $financial['arrieres_total'],
                'taux_collecte' => $financial['taux_recouvrement'],
                'loyers_emis' => $financial['loyers_factures'],
                'loyers_payes' => $financial['loyers_encaisses'],
                'total_impaye' => $financial['arrieres_total'],
                'commission_mensuelle' => round($financial['loyers_encaisses'] * $commissionRate),
                'taux_vacance_economique' => $financial['economic_vacancy_rate'],
                'perte_vacance_economique' => $financial['economic_vacancy_loss'],
            ],
            'revenus_par_mois' => array_map(function ($label, $val) {
                return ['mois' => $label, 'montant' => $val];
            }, $chart['labels'], $chart['encaissements']),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rapport-mensuel', compact('data', 'mois'));

        $filename = 'Rapport_Mensuel_'.Carbon::parse($mois)->translatedFormat('F_Y').'.pdf';

        return $pdf->stream($filename);
    }
}
