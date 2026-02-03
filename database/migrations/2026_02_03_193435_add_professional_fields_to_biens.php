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
        Schema::table('biens', function (Blueprint $table) {
            if (!Schema::hasColumn('biens', 'nombre_pieces')) {
                $table->integer('nombre_pieces')->nullable()->after('type');
            }
            if (!Schema::hasColumn('biens', 'meuble')) {
                $table->boolean('meuble')->default(false)->after('nombre_pieces');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn(['nombre_pieces', 'meuble']);
        });
    }
};
