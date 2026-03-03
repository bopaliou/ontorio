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

    /**
     * Dashboard index — loads ONLY overview + role-specific data.
     * Sections like biens, locataires, contrats etc. are loaded lazily via section().
     */
    public function index()
    {
        Carbon::setLocale('fr');
        $user = auth()->user();

        // Lightweight overview data only
        $overviewData = [
            'alerts' => $this->statsService->getAlerts(),
            'parc_stats' => $this->statsService->getParcStats(),
            'financial_stats' => $this->statsService->getFinancialKPIs(),
            // Empty placeholders — not loaded upfront anymore
            'locataires_all' => [],
            'biens_all' => [],
            // These are needed by the overview section template
            'biens_list' => Bien::select('id', 'nom', 'statut')->get(),
        ];

        $roleData = match ($user->role) {
            'admin' => $this->getAdminData(),
            'gestionnaire' => $this->getGestionnaireData(),
            'comptable' => $this->getComptableData(),
            'direction' => $this->getDirectionData(),
            'proprietaire' => $this->getProprietaireData(),
            default => abort(403, 'Rôle non autorisé'),
        };

        $data = array_merge($overviewData, $roleData);

        return view('dashboard.index', compact('data'));
    }

    /**
     * Lazy-load a single dashboard section via AJAX.
     * Returns rendered HTML for the requested section.
     */
    public function section(string $name)
    {
        Carbon::setLocale('fr');
        $user = auth()->user();

        // Whitelist of allowed sections
        $allowed = [
            'biens', 'proprietaires', 'locataires', 'contrats',
            'loyers', 'paiements', 'depenses', 'relances',
            'utilisateurs', 'logs', 'parametres',
        ];

        if (!in_array($name, $allowed)) {
            abort(404, 'Section inconnue');
        }

        // Admin-only sections
        if (in_array($name, ['utilisateurs', 'logs']) && $user->role !== 'admin') {
            abort(403);
        }

        $data = $this->getSectionData($name, $user);

        // Return rendered HTML fragment
        return view('dashboard.sections.' . $name, compact('data'))->render();
    }

    /**
     * Generate data for a specific section.
     */
    private function getSectionData(string $section, $user): array
    {
        $baseData = [
            'financial_stats' => $this->statsService->getFinancialKPIs(),
            'parc_stats' => $this->statsService->getParcStats(),
            'categories_depenses' => ['maintenance', 'travaux', 'taxe', 'assurance', 'autre'],
        ];

        return match ($section) {
            'biens' => array_merge($baseData, [
                'biens_list' => Bien::with(['contrats.locataire', 'images', 'imagePrincipale', 'proprietaire'])->latest()->paginate(25, ['*'], 'page_biens'),
                'proprietaires_list' => in_array($user->role, ['admin', 'gestionnaire', 'direction'])
                    ? Proprietaire::orderBy('id', 'asc')->get()
                    : collect([]),
            ]),

            'proprietaires' => array_merge($baseData, [
                'proprietaires_list' => in_array($user->role, ['admin', 'gestionnaire', 'direction'])
                    ? Proprietaire::withCount(['biens as logements_count'])->orderBy('id', 'asc')->get()
                    : collect([]),
                'agency' => in_array($user->role, ['admin', 'gestionnaire', 'direction'])
                    ? Proprietaire::where('email', 'contact@ontariogroup.net')->first()
                    : null,
                'owner_stats' => in_array($user->role, ['admin', 'gestionnaire', 'direction'])
                    ? $this->getOptimizedOwnerStats()
                    : collect([]),
            ]),

            'locataires' => array_merge($baseData, [
                'locataires_list' => Locataire::with(['contrats.bien', 'contrats.loyers', 'documents'])->withCount('contrats')->latest()->paginate(25, ['*'], 'page_locataires'),
            ]),

            'contrats' => array_merge($baseData, [
                'contrats_list' => Contrat::with(['bien:id,nom,adresse', 'locataire:id,nom,telephone', 'loyers'])->latest()->paginate(25, ['*'], 'page_contrats'),
                // Needed for the contrat creation form select dropdowns
                'locataires_all' => [],
                'biens_all' => [],
            ]),

            'loyers' => array_merge($baseData, [
                'loyers_list' => Loyer::withMontantPaye()
                    ->with(['contrat.locataire', 'contrat.bien', 'paiements'])
                    ->where(function ($q) {
                        $q->where('statut', '!=', 'payé')
                          ->orWhere('mois', '>=', Carbon::now()->subMonths(2)->format('Y-m'));
                    })
                    ->orderBy('mois', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'page_loyers'),
                'contrats_list' => Contrat::where('statut', 'actif')->with('bien', 'locataire')->get(),
            ]),

            'paiements' => array_merge($baseData, [
                'paiements_list' => Paiement::with(['loyer.contrat.locataire', 'loyer.contrat.bien'])->latest()->paginate(50, ['*'], 'page_paiements'),
                'loyers_list' => Loyer::withMontantPaye()
                    ->with(['contrat.locataire', 'contrat.bien'])
                    ->whereIn('statut', ['émis', 'en_retard', 'partiellement_payé'])
                    ->orderBy('mois', 'desc')
                    ->get(),
            ]),

            'depenses' => array_merge($baseData, [
                'depenses_list' => \App\Models\Depense::with('bien.proprietaire')->latest()->paginate(25, ['*'], 'page_depenses'),
                'biens_list' => Bien::select('id', 'nom')->orderBy('nom')->get(),
            ]),

            'relances' => array_merge($baseData, [
                'loyers_list' => Loyer::withMontantPaye()
                    ->with(['contrat.locataire', 'contrat.bien'])
                    ->whereIn('statut', ['émis', 'en_retard', 'partiellement_payé'])
                    ->orderBy('mois', 'desc')
                    ->get(),
            ]),

            'utilisateurs' => array_merge($baseData, [
                'users_list' => User::all(),
            ]),

            'logs' => array_merge($baseData, [
                'logs_list' => ActivityLog::with('user')->latest()->limit(50)->get(),
            ]),

            'parametres' => $baseData,

            default => $baseData,
        };
    }

    public function refreshCache()
    {
        $this->statsService->clearCache();
        return redirect()->back()->with('success', 'Cache rafraîchi avec succès');
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
        $chartData = $this->statsService->getChartData();
        $commissionRate = (float) config('real_estate.commission.rate', 0.10);

        return [
            'role' => 'direction',
            'kpis' => [
                'total_logements' => $parc['total_biens'],
                'biens_occupes' => $parc['biens_occupes'],
                'taux_occupation' => $parc['taux_occupation'],
                'revenu_mensuel' => $financial['loyers_encaisses'],
                'taux_collecte' => $financial['taux_recouvrement'],
                'commission_mensuelle' => round($financial['loyers_encaisses'] * $commissionRate, 2),
                'impayes' => $financial['arrieres_total'],
                'valeur_portefeuille' => $financial['gross_potential_rent'],
                'loyer_moyen' => $parc['total_biens'] > 0 ? round($financial['gross_potential_rent'] / $parc['total_biens']) : 0,
                'projection_annuelle' => round($financial['loyers_encaisses'] * $commissionRate, 2) * 12,
                'taux_vacance_economique' => $financial['economic_vacancy_rate'],
                'perte_vacance_economique' => $financial['economic_vacancy_loss'],
            ],
            'repartition_type' => DB::table('biens')->select('type', DB::raw('count(*) as total'))->groupBy('type')->get(),
            'revenus_par_mois' => collect($chartData['labels'] ?? [])->map(function ($label, $index) use ($chartData) {
                return [
                    'mois' => $label,
                    'montant' => (float) ($chartData['encaissements'][$index] ?? 0),
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
                'commission_mensuelle' => round($financial['loyers_encaisses'] * $commissionRate, 2),
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

    private function getOptimizedOwnerStats()
    {
        $proprietaires = Proprietaire::withCount([
            'biens as total_units',
            'biens as occupied_units' => function ($query) {
                $query->whereHas('contrats', function ($q) {
                    $q->where('statut', 'actif');
                });
            }
        ])->get();

        return $proprietaires->mapWithKeys(function ($prop) {
            $totalBiens = $prop->total_units;
            $occupes = $prop->occupied_units;
            
            $ca = \App\Models\Paiement::whereHas('loyer.contrat.bien', function($q) use ($prop) {
                $q->where('proprietaire_id', $prop->id);
            })->whereMonth('date_paiement', now()->month)->whereYear('date_paiement', now()->year)->sum('montant');
            
            $arrieresFactures = \App\Models\Loyer::whereHas('contrat.bien', function($q) use ($prop) {
                $q->where('proprietaire_id', $prop->id);
            })->whereIn('statut', ['émis', 'en_retard', 'partiellement_payé'])->selectRaw('SUM(montant + COALESCE(penalite, 0)) as total')->value('total') ?? 0;
            
            $arrieresPayes = \App\Models\Paiement::whereHas('loyer.contrat.bien', function($q) use ($prop) {
                $q->where('proprietaire_id', $prop->id);
            })->whereHas('loyer', function($q) {
                $q->whereIn('statut', ['émis', 'en_retard', 'partiellement_payé']);
            })->sum('montant');

            $arrieres = $arrieresFactures - $arrieresPayes;

            return [$prop->id => [
                'occupancy_rate' => $totalBiens > 0 ? round(($occupes / $totalBiens) * 100, 1) : 0,
                'revenue_this_month' => (float) $ca,
                'total_arrears' => (float) max(0, $arrieres),
                'total_units' => $totalBiens,
                'occupied_units' => $occupes,
            ]];
        });
    }
}
