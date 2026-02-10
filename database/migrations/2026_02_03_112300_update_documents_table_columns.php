<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COL_ENTITE_TYPE = 'entité_type';

    private const COL_ENTITE_ID = 'entité_id';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Ajouter la colonne nom_original
            $table->string('nom_original')->nullable()->after('type');

            // Renommer les colonnes avec accents si elles existent
            if (Schema::hasColumn('documents', self::COL_ENTITE_TYPE)) {
                $table->renameColumn(self::COL_ENTITE_TYPE, 'entite_type');
            }
            if (Schema::hasColumn('documents', self::COL_ENTITE_ID)) {
                $table->renameColumn(self::COL_ENTITE_ID, 'entite_id');
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
                $table->renameColumn('entite_type', self::COL_ENTITE_TYPE);
            }
            if (Schema::hasColumn('documents', 'entite_id')) {
                $table->renameColumn('entite_id', self::COL_ENTITE_ID);
            }
        });
    }
};
