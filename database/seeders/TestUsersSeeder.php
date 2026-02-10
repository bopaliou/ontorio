<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Créer un compte de test pour chaque rôle
     * Tous les mots de passe sont : password
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Gestionnaire Test',
                'email' => 'gestionnaire@test.com',
                'password' => 'password',
                'role' => 'gestionnaire',
            ],
            [
                'name' => 'Comptable Test',
                'email' => 'comptable@test.com',
                'password' => 'password',
                'role' => 'comptable',
            ],
            [
                'name' => 'Direction Test',
                'email' => 'direction@test.com',
                'password' => 'password',
                'role' => 'direction',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info('✅ 4 comptes de test créés avec succès !');
        $this->command->info('📧 Mot de passe pour tous : password');
    }
}
