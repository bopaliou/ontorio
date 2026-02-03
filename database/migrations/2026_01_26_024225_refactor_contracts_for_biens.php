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
        // On modifie la table contrats pour utiliser bien_id au lieu de logement_id
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['logement_id']);
            $table->dropColumn('logement_id');
            $table->foreignId('bien_id')->after('id')->constrained('biens')->onDelete('cascade');
        });

        // On peut maintenant supprimer les anciennes tables
        Schema::dropIfExists('logements');
        Schema::dropIfExists('immeubles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logique inverse (optionnelle ici vu le refactoring majeur)
    }
};
