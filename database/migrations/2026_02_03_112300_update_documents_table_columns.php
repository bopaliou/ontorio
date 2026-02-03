<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Ajouter la colonne nom_original
            $table->string('nom_original')->nullable()->after('type');
            
            // Renommer les colonnes avec accents si elles existent
            if (Schema::hasColumn('documents', 'entité_type')) {
                $table->renameColumn('entité_type', 'entite_type');
            }
            if (Schema::hasColumn('documents', 'entité_id')) {
                $table->renameColumn('entité_id', 'entite_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('nom_original');
            
            if (Schema::hasColumn('documents', 'entite_type')) {
                $table->renameColumn('entite_type', 'entité_type');
            }
            if (Schema::hasColumn('documents', 'entite_id')) {
                $table->renameColumn('entite_id', 'entité_id');
            }
        });
    }
};
