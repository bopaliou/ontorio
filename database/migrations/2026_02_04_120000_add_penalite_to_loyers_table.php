<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ajout des pénalités de retard pour une gestion rigoureuse des impayés
     */
    public function up(): void
    {
        Schema::table('loyers', function (Blueprint $table) {
            $table->decimal('penalite', 10, 2)->default(0)->after('montant');
            $table->decimal('taux_penalite', 5, 2)->default(10)->after('penalite'); // 10% par défaut
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyers', function (Blueprint $table) {
            $table->dropColumn(['penalite', 'taux_penalite']);
        });
    }
};
