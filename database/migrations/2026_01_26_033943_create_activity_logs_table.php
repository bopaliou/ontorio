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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');      // ex: "Création", "Modification", "Suppression"
            $table->string('description'); // ex: "Ajout du bien Villa Ndiaye"
            $table->string('type')->default('info'); // ex: "info", "warning", "danger", "success"
            // Pour lier à un objet (polymorphisme simple)
            $table->string('target_type')->nullable(); // ex: "App\Models\Bien"
            $table->unsignedBigInteger('target_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
