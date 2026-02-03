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
        Schema::create('bien_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
            $table->string('chemin');              // Chemin du fichier dans storage
            $table->string('nom_original');        // Nom original du fichier uploadé
            $table->boolean('principale')->default(false); // Image principale (miniature)
            $table->integer('ordre')->default(0);  // Ordre d'affichage dans la galerie
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bien_images');
    }
};
