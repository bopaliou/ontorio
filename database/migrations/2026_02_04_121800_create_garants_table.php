<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table des garants pour sécuriser les dossiers locataires
     */
    public function up(): void
    {
        Schema::create('garants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locataire_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->text('adresse');
            $table->string('profession')->nullable();
            $table->decimal('revenus_mensuels', 12, 2)->nullable();
            $table->string('piece_identite')->nullable(); // Chemin du fichier CNI
            $table->string('justificatif_revenus')->nullable(); // Chemin du fichier bulletin
            $table->enum('lien_locataire', ['parent', 'employeur', 'autre'])->default('parent');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garants');
    }
};
