<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comptes de test pour chaque rôle
        $this->call([
            TestUsersSeeder::class,
            SenegalDataSeeder::class,
        ]);
    }
}
