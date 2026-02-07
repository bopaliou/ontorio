<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Locataire;
use App\Models\Loyer;
use App\Models\Paiement;
use App\Models\Proprietaire;
use Carbon\Carbon;

/**
 * Service pour centraliser les calculs de statistiques du Dashboard
 * Optimisé pour éviter les N+1 queries
 */
class DashboardStatsService
{
    /**
     * Obtenir les KPIs financiers principaux
     */
    public function getFinancialKPIs(?string $mois = null): array
    {
        $mois = $mois ?? Carbon::now()->format('Y-m');
        $cacheKey = "financial_kpis_{$mois}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function () use ($mois) {
            // Loyers du mois - une seule requête
            $loyersStats = Loyer::where('mois', $mois)
                ->where('statut', '!=', 'annulé')
                ->selectRaw('
                    SUM(montant) as total_facture,
                    SUM(CASE WHEN statut = "payé" THEN montant ELSE 0 END) as total_paye,
                    COUNT(*) as nb_loyers,
                    SUM(CASE WHEN statut = "payé" THEN 1 ELSE 0 END) as nb_payes,
                    SUM(CASE WHEN statut IN ("émis", "en_retard") THEN 1 ELSE 0 END) as nb_impayes
                ')
                ->first();

            // Paiements du mois (Tous les paiements encaissés ce mois, peu importe l'échéance du loyer)
            $paiementsMois = Paiement::whereMonth('date_paiement', Carbon::parse($mois)->month)
                ->whereYear('date_paiement', Carbon::parse($mois)->year)
                ->sum('montant');

            // Dépenses du mois
            $depensesMois = Depense::whereMonth('date_depense', Carbon::parse($mois)->month)
                ->whereYear('date_depense', Carbon::parse($mois)->year)
                ->sum('montant');

            // Arriérés totaux (Tous les loyers impayés ou en retard, incluant le mois actuel)
            $arrieres = Loyer::whereIn('statut', ['émis', 'en_retard', 'partiellement_payé'])
                ->where('statut', '!=', 'annulé')
                ->selectRaw('SUM(montant) - SUM((SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)) as solde_du')
                ->first()
                ->solde_du ?? 0;

            // 4. Gross Potential Rent (Loyer Potentiel Total si 100% loué et payé)
            $grossPotentialRent = Bien::sum('loyer_mensuel'); // Somme de tous les loyers théoriques

            // 5. Taux de Recouvrement Financier (Encaissé / Facturé Réel)
            $tauxRecouvrement = $loyersStats->total_facture > 0
                ? ($paiementsMois / $loyersStats->total_facture) * 100
                : 0;

            // 6. Taux d'Occupation Financier (Facturé / Potentiel)
            $tauxOccupationFinancier = $grossPotentialRent > 0
                ? ($loyersStats->total_facture / $grossPotentialRent) * 100
                : 0;

            // 7. Arrears Aging (Ventilation des arriérés)
            $loyersImpayes = Loyer::whereIn('statut', ['émis', 'en_retard', 'partiellement_payé'])
                ->where('statut', '!=', 'annulé')
                ->withSum('paiements', 'montant')
                ->get();

            $aging = [
                '0-30' => 0,
                '31-60' => 0,
                '61-90' => 0,
                '90+' => 0,
            ];

            foreach ($loyersImpayes as $loyer) {
                $reste = $loyer->montant - ($loyer->paiements_sum_montant ?? 0);
                if ($reste <= 0.5) {
                    continue;
                } // Ignorer les micro-différences

                // Calcul de l'âge de la dette par rapport au 1er du mois du loyer
                // Ex: Loyer Janvier (01-01), Nous sommes le 06-02 = 36 jours
                $dateLoyer = Carbon::parse($loyer->mois.'-01');
                $ageJours = $dateLoyer->diffInDays(Carbon::now());

                if ($ageJours <= 30) {
                    $aging['0-30'] += $reste;
                } elseif ($ageJours <= 60) {
                    $aging['31-60'] += $reste;
                } elseif ($ageJours <= 90) {
                    $aging['61-90'] += $reste;
                } else {
                    $aging['90+'] += $reste;
                }
            }

            return [
                'loyers_factures' => $loyersStats->total_facture ?? 0,
                'loyers_encaisses' => $paiementsMois, // Total réel encaissé
                'paiements_mois' => $paiementsMois,
                'depenses_mois' => $depensesMois,
                'solde_net' => $paiementsMois - $depensesMois, // NOI
                'taux_recouvrement' => round($tauxRecouvrement, 1),
                'nb_loyers' => $loyersStats->nb_loyers ?? 0,
                'nb_payes' => $loyersStats->nb_payes ?? 0,
                'nb_impayes' => $loyersStats->nb_impayes ?? 0,
                'arrieres_total' => $arrieres,
                'kpis_modern' => [
                    'gross_potential_rent' => $grossPotentialRent,
                    'financial_occupancy_rate' => round($tauxOccupationFinancier, 1),
                    'arrears_aging' => $aging,
                ],
            ];
        });
    }

    /**
     * Obtenir les statistiques du parc immobilier
     */
    public function getParcStats(): array
    {
        // Comptages optimisés
        $totalBiens = Bien::count();
        $biensOccupes = Contrat::where('statut', 'actif')
            ->distinct('bien_id')
            ->count('bien_id');

        $biensVacants = $totalBiens - $biensOccupes;
        $tauxOccupation = $totalBiens > 0 ? round(($biensOccupes / $totalBiens) * 100, 1) : 0;

        // Contrats expirant bientôt (60 jours)
        $contratsExpirants = Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now(), now()->addDays(60)])
            ->count();

        // Taux d'Occupation Financier (Facturé / Potentiel)
        $grossPotentialRent = Bien::sum('loyer_mensuel'); // Potentiel total

        // On prend les loyers FACTURÉS du mois en cours pour comparer au potentiel
        $mois = Carbon::now()->format('Y-m');
        $totalFacture = Loyer::where('mois', $mois)->where('statut', '!=', 'annulé')->sum('montant');

        $tauxFinancier = $grossPotentialRent > 0 ? round(($totalFacture / $grossPotentialRent) * 100, 1) : 0;

        return [
            'total_biens' => $totalBiens,
            'biens_occupes' => $biensOccupes,
            'biens_vacants' => $biensVacants,
            'taux_occupation' => $tauxOccupation,
            'taux_occupation_financier' => $tauxFinancier,
            'contrats_expirants' => $contratsExpirants,
            'total_locataires' => Locataire::count(),
            'total_proprietaires' => Proprietaire::count(),
        ];
    }

    /**
     * Obtenir les données pour les graphiques
     */
    public function getChartData(int $moisCount = 6): array
    {
        $labels = [];
        $encaissements = [];
        $depenses = [];

        for ($i = $moisCount - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M Y');

            // Encaissements du mois
            $encaissements[] = Paiement::whereMonth('date_paiement', $date->month)
                ->whereYear('date_paiement', $date->year)
                ->sum('montant');

            // Dépenses du mois
            $depenses[] = Depense::whereMonth('date_depense', $date->month)
                ->whereYear('date_depense', $date->year)
                ->sum('montant');
        }

        return [
            'labels' => $labels,
            'encaissements' => $encaissements,
            'depenses' => $depenses,
        ];
    }

    /**
     * Obtenir les alertes importantes
     */
    public function getAlerts(): array
    {
        $alerts = [];

        // Loyers en retard
        $loyersRetard = Loyer::where('statut', 'en_retard')->count();
        if ($loyersRetard > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'message' => "$loyersRetard loyer(s) en retard de paiement",
                'action' => 'loyers',
            ];
        }

        // Contrats expirant dans 30 jours
        $contratsUrgents = Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now(), now()->addDays(30)])
            ->count();
        if ($contratsUrgents > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'calendar',
                'message' => "$contratsUrgents contrat(s) expire(nt) dans les 30 jours",
                'action' => 'contrats',
            ];
        }

        // Biens vacants
        $biensVacants = Bien::whereDoesntHave('contrats', function ($q) {
            $q->where('statut', 'actif');
        })->count();
        if ($biensVacants > 0) {
            $alerts[] = [
                'type' => 'secondary',
                'icon' => 'home',
                'message' => "$biensVacants bien(s) vacant(s) à louer",
                'action' => 'biens',
            ];
        }

        return $alerts;
    }
}
