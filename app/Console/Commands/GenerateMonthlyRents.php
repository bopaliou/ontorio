<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Loyer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyRents extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'loyers:generate {--mois= : Mois au format YYYY-MM (défaut: mois courant)}';

    /**
     * The console command description.
     */
    protected $description = 'Génère automatiquement les loyers mensuels pour tous les contrats actifs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mois = $this->option('mois') ?? Carbon::now()->format('Y-m');

        $this->info("Génération des loyers pour le mois: {$mois}");

        // Récupérer tous les contrats actifs
        $contratsActifs = Contrat::where('statut', 'actif')->get();

        $created = 0;
        $skipped = 0;

        foreach ($contratsActifs as $contrat) {
            // Vérifier si le loyer existe déjà pour ce mois
            $existant = Loyer::where('contrat_id', $contrat->id)
                ->where('mois', $mois)
                ->exists();

            if ($existant) {
                $skipped++;

                continue;
            }

            // Créer le loyer
            Loyer::create([
                'contrat_id' => $contrat->id,
                'mois' => $mois,
                'montant' => $contrat->loyer_montant,
                'statut' => 'émis',
                'penalite' => 0,
                'taux_penalite' => 10, // 10% par défaut
            ]);

            $created++;
        }

        $this->info("✅ {$created} loyers créés, {$skipped} ignorés (déjà existants)");

        return Command::SUCCESS;
    }
}
