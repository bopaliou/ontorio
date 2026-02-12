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
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'description')) {
                $table->string('description')->nullable()->after('guard_name');
            }
        });

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'description')) {
                $table->string('description')->nullable()->after('guard_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
