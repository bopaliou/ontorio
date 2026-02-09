<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Nettoie les tables legacy (immeubles, logements) qui ont été consolidées
     * dans la table 'biens'. Cette migration s'assure qu'aucune table
     * orpheline ne reste en base de données.
     */
    public function up(): void
    {
        // Vérifier et supprimer les tables legacy si elles existent
        Schema::dropIfExists('logements');
        Schema::dropIfExists('immeubles');

        // Vérifier que contrats pointe bien sur biens
        $this->ensureContratPointsToBiens();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pour protéger les données, on ne peut pas vraiment reverser ce nettoyage
        // Cependant, si nécessaire, on peut recréer les tables
        // (mais ce ne serait que pour la structure, pas les données)
    }

    /**
     * Vérifier que la table contrats pointe bien vers biens
     */
    private function ensureContratPointsToBiens(): void
    {
        // Vérifier que bien_id existe dans contrats
        if (Schema::hasTable('contrats')) {
            if (! Schema::hasColumn('contrats', 'bien_id')) {
                throw new \Exception('La table contrats doit avoir une colonne bien_id pointant vers biens');
            }
        }
    }
};
