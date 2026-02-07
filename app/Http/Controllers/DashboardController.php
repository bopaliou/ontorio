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
    public function index()
    {
        Carbon::setLocale('fr');
        $user = auth()->user();
        $moisActuel = Carbon::now()->format('Y-m');

        // 1. Récupération des stats Ontario Group (Optimisée avec Sous-requêtes)
        // 1. Récupération des stats Ontario Group (Optimisée selon le rôle)
        // Les sous-requêtes financières sont lourdes, on ne les lance que pour Admin/Direction/Gestionnaire
        $proprietaires = collect([]);

        if (in_array($user->role, ['admin', 'gestionnaire', 'direction'])) {
            $proprietaires = Proprietaire::withCount(['biens as logements_count'])
                ->addSelect([
                    'loyers_factures_mois' => Loyer::selectRaw('sum(montant)')
                        ->whereColumn('loyers.contrat_id', 'contrats.id')
                        ->where('loyers.mois', $moisActuel)
                        ->where('loyers.statut', '!=', 'annulé')
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'loyers_encaisses_mois' => Loyer::selectRaw('sum(montant)')
                        ->whereColumn('loyers.contrat_id', 'contrats.id')
                        ->where('loyers.mois', $moisActuel)
                        ->where('loyers.statut', 'payé')
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'total_impayes' => Loyer::selectRaw('SUM(loyers.montant - COALESCE((SELECT SUM(montant) FROM paiements WHERE paiements.loyer_id = loyers.id), 0))')
                        ->whereIn('loyers.statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'total_depenses' => \App\Models\Depense::selectRaw('sum(montant)')
                        ->join('biens', 'depenses.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'total_encaisse_global' => Paiement::selectRaw('sum(paiements.montant)')
                        ->join('loyers', 'paiements.loyer_id', '=', 'loyers.id')
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'depenses_mois' => \App\Models\Depense::selectRaw('sum(montant)')
                        ->where('date_depense', 'like', $moisActuel.'%')
                        ->join('biens', 'depenses.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),
                ])
                ->get();
        } else {
            // Version légère pour Comptable (juste la liste pour info)
            $proprietaires = Proprietaire::withCount(['biens as logements_count'])->limit(50)->get();
        }

        // Données communes optimisées (Pagination systématique et Eager Loading)
        $commonData = [
            'biens_list' => Bien::with(['contrats.locataire', 'images', 'imagePrincipale', 'proprietaire'])
                ->latest()
                ->paginate(25, ['*'], 'page_biens'),
                
            'depenses_list' => \App\Models\Depense::with('bien.proprietaire')
                ->latest()
                ->paginate(25, ['*'], 'page_depenses'),
                
            'categories_depenses' => ['maintenance', 'travaux', 'taxe', 'assurance', 'autre'],
            
            'locataires_list' => Locataire::with(['contrats.bien', 'contrats.loyers'])
                ->withCount('contrats')
                ->latest()
                ->paginate(25, ['*'], 'page_locataires'),
                
            'contrats_list' => Contrat::with(['bien:id,nom,adresse', 'locataire:id,nom,telephone', 'loyers'])
                ->latest()
                ->paginate(25, ['*'], 'page_contrats'),
                
            'loyers_list' => Loyer::withMontantPaye()
                ->with(['contrat.locataire', 'contrat.bien', 'paiements'])
                ->orderBy('id', 'desc')
                ->paginate(50, ['*'], 'page_loyers'),
                
            'paiements_list' => Paiement::with(['loyer.contrat.locataire', 'loyer.contrat.bien'])
                ->latest()
                ->paginate(50, ['*'], 'page_paiements'),
                
            'proprietaires_list' => $proprietaires,
        ];

        switch ($user->role) {
            case 'admin':
                $roleData = $this->getAdminData();
                break;
            case 'gestionnaire':
                $roleData = $this->getGestionnaireData();
                break;
            case 'comptable':
                $roleData = $this->getComptableData();
                break;
            case 'direction':
                $roleData = $this->getDirectionData();
                break;
            case 'proprietaire':
                $roleData = $this->getProprietaireData();
                break;
            default:
                abort(403, 'Rôle non autorisé');
        }

        $data = array_merge($commonData, $roleData);

        return view('dashboard.index', compact('data'));
    }

    private function getAdminData()
    {
        $moisActuel = Carbon::now()->format('Y-m');
        $gestionnaireData = $this->getGestionnaireData();

        return array_merge($gestionnaireData, [
            'role' => 'admin',
            'users_list' => User::all(),
            'logs_list' => ActivityLog::with('user')->latest()->limit(50)->get(),
        ]);
    }

    private function getGestionnaireData()
    {
        $totalBiens = Bien::count();
        $biensLibres = Bien::where('statut', 'libre')->count();
        $contratsActifs = Contrat::where('statut', 'actif')->count();
        $moisActuel = Carbon::now()->format('Y-m');
        $loyersEmisMois = Loyer::where('mois', $moisActuel)->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayesMois = Loyer::where('mois', $moisActuel)->where('statut', 'payé')->sum('montant');
        $loyersEnRetard = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));

        return [
            'role' => 'gestionnaire',
            'kpis' => [
                'total_logements' => $totalBiens,
                'logements_libres' => $biensLibres,
                'contrats_actifs' => $contratsActifs,
                'loyers_emis_mois' => $loyersEmisMois,
                'loyers_payes_mois' => $loyersPayesMois,
                'total_en_retard' => $loyersEnRetard,
            ],
            'derniers_contrats' => Contrat::with(['bien', 'locataire'])->where('statut', 'actif')->latest()->limit(5)->get(),
        ];
    }

    private function getComptableData()
    {
        $moisActuel = Carbon::now()->format('Y-m');
        $loyersEmis = Loyer::where('mois', $moisActuel)->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayes = Loyer::where('mois', $moisActuel)->where('statut', 'payé')->sum('montant');
        $totalImpaye = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));

        return [
            'role' => 'comptable',
            'kpis' => [
                'loyers_emis' => $loyersEmis,
                'loyers_payes' => $loyersPayes,
                'total_impaye' => $totalImpaye,
                'taux_recouvrement' => $loyersEmis > 0 ? round(($loyersPayes / $loyersEmis) * 100) : 0,
            ],
            'loyers_en_attente' => Loyer::with(['contrat.bien', 'contrat.locataire'])->whereIn('statut', ['émis', 'en_retard'])->latest()->limit(10)->get(),
            'derniers_paiements' => Paiement::with(['loyer.contrat.locataire'])->latest('date_paiement')->limit(10)->get(),
        ];
    }

    private function getProprietaireData()
    {
        $user = auth()->user();
        // On suppose que l'utilisateur 'proprietaire' a un lien avec un modèle Proprietaire ou un email correspondant
        // Pour l'instant, on cherche le proprietaire via l'email si ce n'est pas un admin
        $proprietaireId = null;
        if ($user->role === 'proprietaire') {
            // Essayons de trouver le proprietaire lié à ce compte utilisateur
            // (Hypothèse: un utilisateur proprietaire a son email dans la table proprietaires)
            $propRecord = Proprietaire::where('email', $user->email)->first();
            $proprietaireId = $propRecord ? $propRecord->id : null;
        }

        if (! $proprietaireId) {
            return [
                'role' => 'proprietaire',
                'kpis' => ['revenus' => 0, 'charges' => 0, 'net' => 0],
                'biens_performance' => [],
            ];
        }

        $moisActuel = Carbon::now()->format('Y-m');

        // Revenus encaissés ce mois pour ce propriétaire
        $revenusMois = Paiement::whereHas('loyer', function ($q) use ($moisActuel, $proprietaireId) {
            $q->where('mois', $moisActuel)
                ->whereHas('contrat.bien', function ($sq) use ($proprietaireId) {
                    $sq->where('proprietaire_id', $proprietaireId);
                });
        })->sum('montant');

        // Charges (Dépenses) pour ce propriétaire
        $chargesMois = \App\Models\Depense::whereHas('bien', function ($q) use ($proprietaireId) {
            $q->where('proprietaire_id', $proprietaireId);
        })->where('date_depense', 'like', $moisActuel.'%')->sum('montant');

        // Commissions Agence (estimées à 10%)
        $commissionsMois = round($revenusMois * 0.10);

        $netMois = $revenusMois - $chargesMois - $commissionsMois;

        // Performance par bien
        $biensPerformance = Bien::where('proprietaire_id', $proprietaireId)
            ->withCount(['contrats as is_active' => function ($q) {
                $q->where('statut', 'actif');
            }])
            ->addSelect([
                'revenus_cumules' => Paiement::selectRaw('sum(montant)')
                    ->join('loyers', 'paiements.loyer_id', '=', 'loyers.id')
                    ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                    ->whereColumn('contrats.bien_id', 'biens.id'),
                'charges_cumulees' => \App\Models\Depense::selectRaw('sum(montant)')
                    ->whereColumn('depenses.bien_id', 'biens.id'),
            ])
            ->get();

        // Revenus des 6 derniers mois pour le graphique
        $revenusPar6Mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $moisRaw = Carbon::now()->subMonths($i)->format('Y-m');

            $rev = Paiement::whereHas('loyer', function ($q) use ($moisRaw, $proprietaireId) {
                $q->where('mois', $moisRaw)
                    ->whereHas('contrat.bien', function ($sq) use ($proprietaireId) {
                        $sq->where('proprietaire_id', $proprietaireId);
                    });
            })->sum('montant');

            $revenusPar6Mois[] = [
                'mois' => $m,
                'montant' => $rev,
            ];
        }

        return [
            'role' => 'proprietaire',
            'kpis' => [
                'revenu_mensuel' => $revenusMois,
                'charges_mensuelles' => $chargesMois,
                'commissions_mensuelles' => $commissionsMois,
                'net_mensuel' => $netMois,
                'taux_rentabilite' => 0, // Placeholder
            ],
            'biens_list' => $biensPerformance,
            'revenus_par_mois' => $revenusPar6Mois,
        ];
    }

    private function getDirectionData()
    {
        $moisActuel = Carbon::now()->format('Y-m');
        $totalBiens = Bien::count();
        $biensOccupesCount = Bien::where('statut', 'occupé')->count();
        $tauxOccupation = $totalBiens > 0 ? round(($biensOccupesCount / $totalBiens) * 100) : 0;

        $loyersEmisMois = Loyer::where('mois', $moisActuel)->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayesMois = Loyer::where('mois', $moisActuel)->where('statut', 'payé')->sum('montant');
        $tauxCollecte = $loyersEmisMois > 0 ? round(($loyersPayesMois / $loyersEmisMois) * 100) : 0;

        $impayesTotal = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));
        $valeurPortefeuille = Bien::sum('loyer_mensuel');

        $commissionMensuelle = round($loyersPayesMois * 0.10);

        // Répartition par type de bien
        $repartitionType = DB::table('biens')
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        // Revenus des 6 derniers mois pour le graphique
        $revenusPar6Mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $moisRaw = Carbon::now()->subMonths($i)->format('Y-m');
            $revenusPar6Mois[] = [
                'mois' => $m,
                'montant' => Loyer::where('mois', $moisRaw)->where('statut', 'payé')->sum('montant'),
            ];
        }

        return [
            'role' => 'direction',
            'kpis' => [
                'total_logements' => $totalBiens,
                'biens_occupes' => $biensOccupesCount,
                'taux_occupation' => $tauxOccupation,
                'revenu_mensuel' => $loyersPayesMois,
                'taux_collecte' => $tauxCollecte,
                'commission_mensuelle' => $commissionMensuelle,
                'impayes' => $impayesTotal,
                'valeur_portefeuille' => $valeurPortefeuille,
                'loyer_moyen' => $totalBiens > 0 ? round($valeurPortefeuille / $totalBiens) : 0,
                'projection_annuelle' => $commissionMensuelle * 12,
            ],
            'repartition_type' => $repartitionType,
            'revenus_par_mois' => $revenusPar6Mois,
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

        // Calcul des KPIs
        $totalBiens = Bien::count();
        $biensOccupes = Bien::where('statut', 'occupé')->count();
        $tauxOccupation = $totalBiens > 0 ? round(($biensOccupes / $totalBiens) * 100) : 0;

        $loyersMois = Loyer::where('mois', $mois)->get();
        $loyersEmis = $loyersMois->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayes = $loyersMois->where('statut', 'payé')->sum('montant');
        $totalImpaye = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));

        $tauxCollecte = $loyersEmis > 0 ? round(($loyersPayes / $loyersEmis) * 100) : 0;
        $commissionMensuelle = round($loyersPayes * 0.10); // 10% de commission

        // Revenus des 6 derniers mois
        $revenusPar6Mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $mRaw = Carbon::now()->subMonths($i)->format('Y-m');
            $revenusPar6Mois[] = [
                'mois' => $m,
                'montant' => Loyer::where('mois', $mRaw)->where('statut', 'payé')->sum('montant'),
            ];
        }

        $data = [
            'biens_list' => Bien::latest()->get(),
            'kpis' => [
                'revenu_mensuel' => $loyersPayes,
                'taux_occupation' => $tauxOccupation,
                'impayes' => $totalImpaye,
                'taux_collecte' => $tauxCollecte,
                'loyers_emis' => $loyersEmis,
                'loyers_payes' => $loyersPayes,
                'total_impaye' => $totalImpaye,
                'commission_mensuelle' => $commissionMensuelle,
            ],
            'revenus_par_mois' => $revenusPar6Mois,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rapport-mensuel', compact('data', 'mois'));

        $filename = 'Rapport_Mensuel_'.Carbon::parse($mois)->translatedFormat('F_Y').'.pdf';

        return $pdf->stream($filename);
    }
}
