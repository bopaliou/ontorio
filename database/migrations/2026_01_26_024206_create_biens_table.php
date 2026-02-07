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
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->onDelete('cascade');
            $table->string('nom'); // Ex: Appartement 101, Villa Almadies
            $table->string('adresse')->nullable();
            $table->string('ville')->default('Dakar');
            $table->enum('type', ['appartement', 'villa', 'studio', 'bureau', 'magasin', 'entrepot', 'immeuble', 'autre'])->default('appartement');
            $table->decimal('surface', 10, 2)->nullable();
            $table->enum('statut', ['libre', 'occupé', 'en_travaux', 'réservé'])->default('libre');
            $table->decimal('loyer_mensuel', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
