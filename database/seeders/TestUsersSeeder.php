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
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assigner le rôle Spatie correspondant
            if (isset($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }

        $this->command->info('✅ Comptes de test vérifiés/créés et rôles assignés !');
        $this->command->info('📧 Mot de passe pour tous : password');
    }
}
