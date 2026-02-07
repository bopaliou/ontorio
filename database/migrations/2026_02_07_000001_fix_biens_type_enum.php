<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute 'immeuble' à l'enum 'type' de la table 'biens'
     * Raison: Le seeder MockDataSeeder essaie d'insérer la valeur 'immeuble'
     *         qui n'est pas dans l'enum actuel
     */
    public function up(): void
    {
        // Utiliser du SQL brut pour modifier l'ENUM (Laravel Blueprint ne supporte pas bien les ENUM)
        // SQLite (tests) ne supporte pas MODIFY - sauter la modification en mémoire
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE biens MODIFY COLUMN type ENUM('appartement', 'villa', 'studio', 'bureau', 'magasin', 'entrepot', 'immeuble', 'autre') DEFAULT 'appartement'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ENUM original (sans 'immeuble')
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE biens MODIFY COLUMN type ENUM('appartement', 'villa', 'studio', 'bureau', 'magasin', 'entrepot', 'autre') DEFAULT 'appartement'");
    }
};
