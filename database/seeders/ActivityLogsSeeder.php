<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        if ($admin) {
            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'Initialisation Système',
                'description' => 'Déploiement de la version 1.0',
                'type' => 'info',
            ]);
            
            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'Configuration',
                'description' => 'Création des rôles par défaut',
                'type' => 'success',
            ]);
        }
    }
}
