<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loyer;
use Carbon\Carbon;

class CalculatePenalties extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'loyers:penalites {--dry-run : Affiche les pénalités sans les appliquer}';

    /**
     * The console command description.
     */
    protected $description = 'Calcule et applique les pénalités de retard sur les loyers impayés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info($dryRun ? "Mode simulation (dry-run)" : "Application des pénalités...");
        
        // Récupérer les loyers impayés (émis ou en_retard)
        $loyersImpayes = Loyer::whereIn('statut', ['émis', 'en_retard', 'partiel'])
            ->get();
        
        $updated = 0;
        $totalPenalites = 0;
        
        $this->table(['ID', 'Mois', 'Montant', 'Jours Retard', 'Pénalité Calculée'], 
            $loyersImpayes->filter(fn($l) => $l->jours_retard > 0)->map(function ($loyer) use ($dryRun, &$updated, &$totalPenalites) {
                $tauxMensuel = ($loyer->taux_penalite ?? 10) / 100;
                $moisRetard = min(ceil($loyer->jours_retard / 30), 3);
                $penalite = round($loyer->montant * $tauxMensuel * $moisRetard, 2);
                
                if (!$dryRun && $penalite > 0) {
                    $loyer->penalite = $penalite;
                    
                    // Mettre à jour le statut si pas déjà en retard
                    if ($loyer->statut === 'émis') {
                        $loyer->statut = 'en_retard';
                    }
                    
                    $loyer->save();
                    $updated++;
                }
                
                $totalPenalites += $penalite;
                
                return [
                    $loyer->id,
                    $loyer->mois,
                    number_format($loyer->montant, 0, ',', ' ') . ' F',
                    $loyer->jours_retard,
                    number_format($penalite, 0, ',', ' ') . ' F',
                ];
            })->toArray()
        );
        
        $this->info("Total pénalités: " . number_format($totalPenalites, 0, ',', ' ') . " F");
        
        if (!$dryRun) {
            $this->info("✅ {$updated} loyers mis à jour avec pénalités");
        } else {
            $this->warn("⚠️ Mode simulation - Aucune modification effectuée");
            $this->info("Exécutez sans --dry-run pour appliquer les pénalités");
        }
        
        return Command::SUCCESS;
    }
}
