<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Historique des révisions de loyer pour traçabilité des augmentations
     */
    public function up(): void
    {
        Schema::create('revisions_loyer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained()->onDelete('cascade');
            $table->decimal('ancien_montant', 12, 2);
            $table->decimal('nouveau_montant', 12, 2);
            $table->date('date_effet');
            $table->enum('motif', [
                'indexation_annuelle',      // Augmentation légale annuelle
                'travaux_amelioration',     // Après travaux d'amélioration
                'renouvellement_bail',      // Lors du renouvellement
                'accord_parties',           // Accord amiable
                'revision_marche',          // Alignement sur le marché
                'autre'
            ])->default('indexation_annuelle');
            $table->decimal('pourcentage_augmentation', 5, 2)->nullable();
            $table->text('justification')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisions_loyer');
    }
};
