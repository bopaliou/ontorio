<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Ajout d'index pour optimiser les performances
     */
    public function up(): void
    {
        // Index sur la table loyers
        Schema::table('loyers', function (Blueprint $table) {
            $table->index('mois');
            $table->index('statut');
            $table->index(['contrat_id', 'mois']);
            $table->index(['statut', 'mois']);
        });

        // Index sur la table contrats
        Schema::table('contrats', function (Blueprint $table) {
            $table->index('statut');
            $table->index(['bien_id', 'statut']);
            $table->index(['locataire_id', 'statut']);
        });

        // Index sur la table biens
        Schema::table('biens', function (Blueprint $table) {
            $table->index('statut');
            $table->index('proprietaire_id');
        });

        // Index sur la table paiements
        Schema::table('paiements', function (Blueprint $table) {
            $table->index('loyer_id');
            $table->index('date_paiement');
        });

        // Index sur la table documents
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['entite_type', 'entite_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyers', function (Blueprint $table) {
            $table->dropIndex(['mois']);
            $table->dropIndex(['statut']);
            $table->dropIndex(['contrat_id', 'mois']);
            $table->dropIndex(['statut', 'mois']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->dropIndex(['statut']);
            $table->dropIndex(['bien_id', 'statut']);
            $table->dropIndex(['locataire_id', 'statut']);
        });

        Schema::table('biens', function (Blueprint $table) {
            $table->dropIndex(['statut']);
            $table->dropIndex(['proprietaire_id']);
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropIndex(['loyer_id']);
            $table->dropIndex(['date_paiement']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['entite_type', 'entite_id']);
        });
    }
};
