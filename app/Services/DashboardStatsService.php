<?php

namespace App\Services;

use App\Enums\ContratStatus;
use App\Enums\LoyerStatus;
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
     * Obtenir les KPIs financiers principaux pour un mois donné
     */
    public function getFinancialKPIs(?string $mois = null): array
    {
        $mois = $mois ?? Carbon::now()->format('Y-m');
        $cacheKey = "dashboard_financial_kpis_{$mois}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHours(1), function () use ($mois) {
            // Loyers du mois
            $loyersStats = Loyer::where('mois', $mois)
                ->where('statut', '!=', LoyerStatus::ANNULE->value)
                ->selectRaw('
                    SUM(montant) as total_facture,
                    SUM(CASE WHEN statut = "payé" THEN montant ELSE 0 END) as total_paye,
                    COUNT(*) as nb_loyers,
                    SUM(CASE WHEN statut = "payé" THEN 1 ELSE 0 END) as nb_payes,
                    SUM(CASE WHEN statut IN ("émis", "en_retard") THEN 1 ELSE 0 END) as nb_impayes
                ')
                ->first();

            $dateObj = Carbon::parse($mois);
            $paiementsMois = $this->sumForMonth(Paiement::query(), 'date_paiement', $dateObj);
            $depensesMois = $this->sumForMonth(Depense::query(), 'date_depense', $dateObj);

            // Paiements pour les loyers du mois spécifique (pas tous les paiements du mois)
            $paiementsPourLoyersMois = Paiement::whereHas('loyer', function ($q) use ($mois) {
                $q->where('mois', $mois);
            })->sum('montant');

            $arrieres = Loyer::whereIn('statut', [LoyerStatus::EMIS->value, LoyerStatus::EN_RETARD->value, LoyerStatus::PARTIELLEMENT_PAYE->value])
                ->where('statut', '!=', LoyerStatus::ANNULE->value)
                ->selectRaw('SUM(montant + COALESCE(penalite, 0)) - SUM((SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)) as solde_du')
                ->first()
                ->solde_du ?? 0;

            $grossPotentialRent = Bien::sum('loyer_mensuel');
            $tauxRecouvrement = $loyersStats->total_facture > 0 ? ($paiementsPourLoyersMois / $loyersStats->total_facture) * 100 : 0;
            $tauxOccupationFinancier = $grossPotentialRent > 0 ? ($loyersStats->total_facture / $grossPotentialRent) * 100 : 0;
            $vacanceEconomiqueMontant = max(0, $grossPotentialRent - ($loyersStats->total_facture ?? 0));
            $tauxVacanceEconomique = $grossPotentialRent > 0 ? ($vacanceEconomiqueMontant / $grossPotentialRent) * 100 : 0;

            return [
                'loyers_factures' => (float) ($loyersStats->total_facture ?? 0),
                'loyers_encaisses' => (float) $paiementsMois,
                'depenses_mois' => (float) $depensesMois,
                'solde_net' => (float) ($paiementsMois - $depensesMois),
                'taux_recouvrement' => round($tauxRecouvrement, 1),
                'nb_loyers' => $loyersStats->nb_loyers ?? 0,
                'nb_payes' => $loyersStats->nb_payes ?? 0,
                'nb_impayes' => $loyersStats->nb_impayes ?? 0,
                'arrieres_total' => (float) $arrieres,
                'gross_potential_rent' => (float) $grossPotentialRent,
                'financial_occupancy_rate' => round($tauxOccupationFinancier, 1),
                'economic_vacancy_loss' => (float) $vacanceEconomiqueMontant,
                'economic_vacancy_rate' => round($tauxVacanceEconomique, 1),
                'arrears_aging' => $this->calculateArrearsAging(),
            ];
        });
    }

    /**
     * Obtenir les statistiques du parc immobilier
     */
    public function getParcStats(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('dashboard_parc_stats', now()->addHours(1), function () {
            $totalBiens = Bien::count();
            $biensOccupes = Contrat::where('statut', ContratStatus::ACTIF->value)->distinct('bien_id')->count('bien_id');
            $biensVacants = $totalBiens - $biensOccupes;

            return [
                'total_biens' => $totalBiens,
                'biens_occupes' => $biensOccupes,
                'biens_vacants' => $biensVacants,
                'taux_occupation' => $totalBiens > 0 ? round(($biensOccupes / $totalBiens) * 100, 1) : 0,
                'contrats_expirants' => Contrat::where('statut', ContratStatus::ACTIF->value)
                    ->whereNotNull('date_fin')
                    ->whereBetween('date_fin', [now(), now()->addDays(60)])
                    ->count(),
                'total_locataires' => Locataire::count(),
                'total_proprietaires' => Proprietaire::count(),
            ];
        });
    }

    /**
     * Obtenir les statistiques spécifiques pour un propriétaire
     */
    public function getProprietaireStats(Proprietaire $proprietaire): array
    {
        $cacheKey = "dashboard_proprietaire_stats_{$proprietaire->id}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHours(1), function () use ($proprietaire) {
            $moisActuel = Carbon::now()->format('Y-m');

            $revenusMois = Paiement::whereHas('loyer', function ($q) use ($moisActuel, $proprietaire) {
                $q->where('mois', $moisActuel)
                    ->whereHas('contrat.bien', function ($sq) use ($proprietaire) {
                        $sq->where('proprietaire_id', $proprietaire->id);
                    });
            })->sum('montant');

            $chargesMois = \App\Models\Depense::whereHas('bien', function ($q) use ($proprietaire) {
                $q->where('proprietaire_id', $proprietaire->id);
            })->where('date_depense', 'like', $moisActuel.'%')->sum('montant');

            $commissionRate = (float) config('real_estate.commission.rate', 0.10);
            $commissionMois = round($revenusMois * $commissionRate);

            $biensPerformance = Bien::where('proprietaire_id', $proprietaire->id)
                ->withCount(['contrats as is_active' => fn ($q) => $q->where('statut', ContratStatus::ACTIF->value)])
                ->addSelect([
                    'revenus_cumules' => Paiement::selectRaw('sum(montant)')
                        ->join('loyers', 'paiements.loyer_id', '=', 'loyers.id')
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->whereColumn('contrats.bien_id', 'biens.id'),
                    'charges_cumulees' => Depense::selectRaw('sum(montant)')
                        ->whereColumn('depenses.bien_id', 'biens.id'),
                ])
                ->get();

            return [
                'revenu_mensuel' => (float) $revenusMois,
                'charges_mensuelles' => (float) $chargesMois,
                'commissions_mensuelles' => (float) $commissionMois,
                'net_mensuel' => (float) ($revenusMois - $chargesMois - $commissionMois),
                'biens_performance' => $biensPerformance,
                'chart_data' => $this->getProprietaireChartData($proprietaire),
            ];
        });
    }

    /**
     * Obtenir les données pour les graphiques globaux
     */
    public function getChartData(int $moisCount = 6): array
    {
        return \Illuminate\Support\Facades\Cache::remember("dashboard_chart_data_{$moisCount}", now()->addHours(1), function () use ($moisCount) {
            $labels = [];
            $encaissements = [];
            $depenses = [];

            for ($i = $moisCount - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->translatedFormat('M Y');

                $encaissements[] = $this->sumForMonth(Paiement::query(), 'date_paiement', $date);
                $depenses[] = $this->sumForMonth(Depense::query(), 'date_depense', $date);
            }

            return [
                'labels' => $labels,
                'encaissements' => $encaissements,
                'depenses' => $depenses,
            ];
        });
    }

    /**
     * Obtenir les alertes importantes
     */
    public function getAlerts(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('dashboard_alerts', now()->addMinutes(30), function () {
            $alerts = [];

            $loyersRetard = Loyer::where('statut', LoyerStatus::EN_RETARD->value)->count();
            if ($loyersRetard > 0) {
                $alerts[] = ['type' => 'warning', 'icon' => 'exclamation-triangle', 'message' => "$loyersRetard loyer(s) en retard", 'action' => 'loyers'];
            }

            $contratsUrgents = Contrat::where('statut', ContratStatus::ACTIF->value)->whereNotNull('date_fin')->whereBetween('date_fin', [now(), now()->addDays(30)])->count();
            if ($contratsUrgents > 0) {
                $alerts[] = ['type' => 'info', 'icon' => 'calendar', 'message' => "$contratsUrgents contrat(s) expirant bientôt", 'action' => 'contrats'];
            }

            $biensVacants = Bien::whereDoesntHave('contrats', fn ($q) => $q->where('statut', ContratStatus::ACTIF->value))->count();
            if ($biensVacants > 0) {
                $alerts[] = ['type' => 'secondary', 'icon' => 'home', 'message' => "$biensVacants bien(s) vacant(s)", 'action' => 'biens'];
            }

            return $alerts;
        });
    }

    /**
     * Nettoyer tout le cache lié au Dashboard
     */
    public function clearCache(): void
    {
        // En driver 'database', on ne peut pas utiliser de tags, on doit lister ou vider par préfixe si possible.
        // Ici on va vider les clés principales.
        \Illuminate\Support\Facades\Cache::forget('dashboard_parc_stats');
        \Illuminate\Support\Facades\Cache::forget('dashboard_alerts');
        \Illuminate\Support\Facades\Cache::forget('dashboard_chart_data_6');

        // On ne peut pas facilement tout oublier sans tags, mais vider les clés globales est un bon début.
        // Pour les clés par mois ou par proprietaire, elles expireront ou seront écrasées.
        // Idéalement on viderait tout le cache si c'est acceptable, ou on utiliserait un driver supportant les tags.
    }

    private function sumForMonth($query, string $dateColumn, Carbon $date): float
    {
        return (float) $query
            ->whereMonth($dateColumn, $date->month)
            ->whereYear($dateColumn, $date->year)
            ->sum('montant');
    }

    protected function calculateArrearsAging(): array
    {
        $loyersImpayes = Loyer::whereIn('statut', [LoyerStatus::EMIS->value, LoyerStatus::EN_RETARD->value, LoyerStatus::PARTIELLEMENT_PAYE->value])
            ->where('statut', '!=', LoyerStatus::ANNULE->value)
            ->withSum('paiements', 'montant')
            ->get();

        $aging = ['0-30' => 0, '31-60' => 0, '61-90' => 0, '90+' => 0];

        foreach ($loyersImpayes as $loyer) {
            $reste = ($loyer->montant + ($loyer->penalite ?? 0)) - ($loyer->paiements_sum_montant ?? 0);
            if ($reste <= 0.5) {
                continue;
            }

            $dateEcheance = $loyer->date_echeance;
            if (!$dateEcheance) {
                continue;
            }

            if (Carbon::now()->lte($dateEcheance)) {
                $aging['0-30'] += $reste;
                continue;
            }

            $ageJours = $dateEcheance->diffInDays(Carbon::now());

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

        return $aging;
    }

    protected function getProprietaireChartData(Proprietaire $proprietaire): array
    {
        $revenusPar6Mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $moisRaw = Carbon::now()->subMonths($i)->format('Y-m');

            $rev = Paiement::whereHas('loyer', function ($q) use ($moisRaw, $proprietaire) {
                $q->where('mois', $moisRaw)
                    ->whereHas('contrat.bien', function ($sq) use ($proprietaire) {
                        $sq->where('proprietaire_id', $proprietaire->id);
                    });
            })->sum('montant');

            $revenusPar6Mois[] = ['mois' => $m, 'montant' => (float) $rev];
        }

        return $revenusPar6Mois;
    }
}
