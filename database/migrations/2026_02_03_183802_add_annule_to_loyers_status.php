<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On utilise DB::statement car SQLite ne supporte pas bien le changement d'ENUM via Blueprint
        // Et MariaDB/MySQL nécessite une syntaxe spécifique.
        // Pour une compatibilité maximale en Laravel 11/DBAL 3+
        Schema::table('loyers', function (Blueprint $table) {
            $table->string('statut')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyers', function (Blueprint $table) {
             $table->enum('statut', ['émis', 'partiellement_payé', 'payé', 'en_retard'])->default('émis')->change();
        });
    }
};
