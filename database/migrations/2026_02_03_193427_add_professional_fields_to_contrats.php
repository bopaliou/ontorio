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
        Schema::table('contrats', function (Blueprint $table) {
            if (!Schema::hasColumn('contrats', 'caution')) {
                $table->decimal('caution', 10, 2)->nullable()->after('loyer_montant');
            }
            if (!Schema::hasColumn('contrats', 'frais_dossier')) {
                $table->decimal('frais_dossier', 10, 2)->nullable()->after('caution');
            }
            if (!Schema::hasColumn('contrats', 'type_bail')) {
                $table->string('type_bail')->default('habitation')->after('frais_dossier');
            }
            if (!Schema::hasColumn('contrats', 'date_signature')) {
                $table->date('date_signature')->nullable()->after('date_fin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn(['caution', 'frais_dossier', 'type_bail', 'date_signature']);
        });
    }
};
