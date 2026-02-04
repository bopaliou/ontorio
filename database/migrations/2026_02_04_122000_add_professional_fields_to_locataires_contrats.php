<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ajout des champs professionnels aux locataires et contrats
     */
    public function up(): void
    {
        // Champs locataires
        Schema::table('locataires', function (Blueprint $table) {
            if (! Schema::hasColumn('locataires', 'profession')) {
                $table->string('profession')->nullable()->after('adresse');
            }
            if (! Schema::hasColumn('locataires', 'revenus_mensuels')) {
                $table->decimal('revenus_mensuels', 12, 2)->nullable()->after('profession');
            }
        });

        // Champs contrats
        Schema::table('contrats', function (Blueprint $table) {
            if (! Schema::hasColumn('contrats', 'renouvellement_auto')) {
                $table->boolean('renouvellement_auto')->default(false)->after('type_bail');
            }
            if (! Schema::hasColumn('contrats', 'preavis_mois')) {
                $table->integer('preavis_mois')->default(3)->after('renouvellement_auto');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locataires', function (Blueprint $table) {
            $table->dropColumn(['profession', 'revenus_mensuels']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn(['renouvellement_auto', 'preavis_mois']);
        });
    }
};
