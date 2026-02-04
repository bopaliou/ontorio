<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter les nouvelles colonnes si elles n'existent pas
        Schema::table('documents', function (Blueprint $table) {
            if (! Schema::hasColumn('documents', 'entite_type')) {
                $table->string('entite_type')->after('chemin_fichier');
            }
            if (! Schema::hasColumn('documents', 'entite_id')) {
                $table->unsignedBigInteger('entite_id')->after('entite_type');
            }
        });

        // Copier les données des anciennes colonnes (avec accents) vers les nouvelles
        $columns = Schema::getColumnListing('documents');

        // Vérifier si les colonnes avec accents existent et copier les données
        if (in_array('entité_type', $columns) && in_array('entite_type', $columns)) {
            DB::statement('UPDATE documents SET entite_type = `entité_type` WHERE entite_type IS NULL OR entite_type = ""');
        }
        if (in_array('entité_id', $columns) && in_array('entite_id', $columns)) {
            DB::statement('UPDATE documents SET entite_id = `entité_id` WHERE entite_id IS NULL OR entite_id = 0');
        }

        // Supprimer les anciennes colonnes avec accents
        Schema::table('documents', function (Blueprint $table) use ($columns) {
            if (in_array('entité_type', $columns)) {
                $table->dropColumn('entité_type');
            }
            if (in_array('entité_id', $columns)) {
                $table->dropColumn('entité_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Impossible de restaurer les colonnes avec accents
    }
};
