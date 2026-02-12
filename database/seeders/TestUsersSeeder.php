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
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'], // Sera haché automatiquement via le cast du modèle User
                    'role' => $userData['role']
                ]
            );

            // S'assurer que les rôles Spatie sont bien assignés
            if (isset($userData['role'])) {
                // On donne tous les droits à l'admin, et les rôles spécifiques aux autres
                if (!$user->hasRole($userData['role'])) {
                    $user->assignRole($userData['role']);
                }
            }
        }

        $this->command->info('✅ Comptes de test vérifiés/créés et rôles assignés !');
        $this->command->info('📧 Mot de passe pour tous : password');
    }
}
