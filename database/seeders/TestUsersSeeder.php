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
                    'role' => $userData['role'],
                ]
            );

            // S'assurer que les rôles Spatie sont bien assignés
            if (isset($userData['role'])) {
                try {
                    // Check if role exists for web guard, creating it if missing (fallback)
                    $roleName = $userData['role'];
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

                    if (! $user->hasRole($roleName)) {
                        $user->assignRole($role);
                    }
                } catch (\Exception $e) {
                    $this->command->error("Erreur lors de l'assignation du rôle {$userData['role']} à {$userData['email']} : ".$e->getMessage());
                }
            }
        }

        $this->command->info('✅ Comptes de test vérifiés/créés et rôles assignés !');
        $this->command->info('📧 Mot de passe pour tous : password');
    }
}
