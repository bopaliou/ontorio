<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Contrat;
use App\Models\Loyer;
use App\Models\RevisionLoyer;
use Carbon\Carbon;

/**
 * Service pour la gestion des loyers
 * Encapsule la logique métier de génération et révision des loyers
 */
class LoyerService
{
    /**
     * Générer les loyers pour un mois donné
     */
    public function genererLoyersMensuels(?string $mois = null): array
    {
        $mois = $mois ?? Carbon::now()->format('Y-m');

        $contrats = Contrat::where('statut', 'actif')
            ->with('bien:id,nom')
            ->get();

        $generes = 0;
        $existants = 0;
        $erreurs = [];

        foreach ($contrats as $contrat) {
            // Vérifier si le loyer existe déj�
            $existe = Loyer::where('contrat_id', $contrat->id)
                ->where('mois', $mois)
                ->exists();

            if ($existe) {
                $existants++;

                continue;
            }

            try {
                Loyer::create([
                    'contrat_id' => $contrat->id,
                    'mois' => $mois,
                    'montant' => $contrat->loyer_montant,
                    'commission' => $contrat->loyer_montant * 0.10, // 10% commission
                    'statut' => 'émis',
                ]);
                $generes++;
            } catch (\Exception $e) {
                $erreurs[] = "Contrat #{$contrat->id}: ".$e->getMessage();
            }
        }

        if ($generes > 0) {
            ActivityLogger::log(
                'Génération Loyers',
                "Génération automatique: $generes loyers créés pour $mois",
                'success'
            );
        }

        return [
            'mois' => $mois,
            'generes' => $generes,
            'existants' => $existants,
            'erreurs' => $erreurs,
            'total_contrats' => $contrats->count(),
        ];
    }

    /**
     * Réviser le loyer d'un contrat avec traçabilité
     */
    public function reviserLoyer(Contrat $contrat, float $nouveauMontant, string $motif = 'indexation_annuelle', ?string $justification = null): RevisionLoyer
    {
        $ancienMontant = $contrat->loyer_montant;

        // Créer l'historique
        $revision = RevisionLoyer::create([
            'contrat_id' => $contrat->id,
            'ancien_montant' => $ancienMontant,
            'nouveau_montant' => $nouveauMontant,
            'date_effet' => now(),
            'motif' => $motif,
            'justification' => $justification,
            'created_by' => auth()->id(),
        ]);

        // Mettre à jour le contrat
        $contrat->update(['loyer_montant' => $nouveauMontant]);

        // Mettre à jour les loyers futurs non payés
        Loyer::where('contrat_id', $contrat->id)
            ->where('mois', '>=', now()->format('Y-m'))
            ->whereIn('statut', ['émis', 'en_retard'])
            ->update(['montant' => $nouveauMontant]);

        ActivityLogger::log(
            'Révision Loyer',
            'Loyer révisé de '.number_format($ancienMontant, 0, ',', ' ').
            ' à '.number_format($nouveauMontant, 0, ',', ' ').' FCFA',
            'info',
            $contrat
        );

        return $revision;
    }

    /**
     * Calculer et appliquer les pénalités de retard
     */
    public function appliquerPenalites(bool $dryRun = false): array
    {
        $loyersEnRetard = Loyer::whereIn('statut', ['émis', 'en_retard'])
            ->where('mois', '<', Carbon::now()->subMonth()->format('Y-m'))
            ->get();

        $resultats = [];
        $totalPenalites = 0;

        foreach ($loyersEnRetard as $loyer) {
            $penalite = $loyer->calculerPenalite();

            if ($penalite > 0) {
                if (! $dryRun && $loyer->statut !== 'en_retard') {
                    $loyer->update(['statut' => 'en_retard']);
                }

                $resultats[] = [
                    'loyer_id' => $loyer->id,
                    'mois' => $loyer->mois,
                    'montant' => $loyer->montant,
                    'penalite' => $penalite,
                    'jours_retard' => $loyer->jours_retard,
                ];
                $totalPenalites += $penalite;
            }
        }

        if (! $dryRun && count($resultats) > 0) {
            ActivityLogger::log(
                'Application Pénalités',
                count($resultats).' pénalités appliquées pour un total de '.
                number_format($totalPenalites, 0, ',', ' ').' FCFA',
                'warning'
            );
        }

        return [
            'dry_run' => $dryRun,
            'nb_penalites' => count($resultats),
            'total_penalites' => $totalPenalites,
            'details' => $resultats,
        ];
    }
}
