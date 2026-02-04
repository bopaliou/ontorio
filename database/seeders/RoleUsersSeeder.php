<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUsersSeeder extends Seeder
{
    /**
     * Créer un compte de test pour chaque rôle
     */
    public function run(): void
    {
        // Nettoyer les utilisateurs de test existants
        User::whereIn('email', [
            'admin@ontariogroup.sn',
            'gestionnaire@ontariogroup.sn',
            'comptable@ontariogroup.sn',
            'direction@ontariogroup.sn',
        ])->delete();

        // 1. Admin - Accès complet
        User::create([
            'name' => 'Administrateur Ontario',
            'email' => 'admin@ontariogroup.sn',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. Gestionnaire - Gestion opérationnelle
        User::create([
            'name' => 'Amadou Diallo',
            'email' => 'gestionnaire@ontariogroup.sn',
            'password' => Hash::make('gestionnaire123'),
            'role' => 'gestionnaire',
            'email_verified_at' => now(),
        ]);

        // 3. Comptable - Finance et encaissements
        User::create([
            'name' => 'Fatou Sall',
            'email' => 'comptable@ontariogroup.sn',
            'password' => Hash::make('comptable123'),
            'role' => 'comptable',
            'email_verified_at' => now(),
        ]);

        // 4. Direction - Vision stratégique
        User::create([
            'name' => 'Moussa Ndiaye',
            'email' => 'direction@ontariogroup.sn',
            'password' => Hash::make('direction123'),
            'role' => 'direction',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ 4 comptes de test créés avec succès !');
        $this->command->newLine();
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe', 'Nom'],
            [
                ['Admin', 'admin@ontariogroup.sn', 'admin123', 'Administrateur Ontario'],
                ['Gestionnaire', 'gestionnaire@ontariogroup.sn', 'gestionnaire123', 'Amadou Diallo'],
                ['Comptable', 'comptable@ontariogroup.sn', 'comptable123', 'Fatou Sall'],
                ['Direction', 'direction@ontariogroup.sn', 'direction123', 'Moussa Ndiaye'],
            ]
        );
    }
}
