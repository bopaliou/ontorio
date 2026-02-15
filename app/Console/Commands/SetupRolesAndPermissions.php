<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SetupRolesAndPermissions extends Command
{
    protected $signature = 'app:setup-roles-permissions {--force : Force the reset of roles and permissions}';

    protected $description = 'Configure les rôles et permissions de base selon la matrice de sécurité';

    public function handle()
    {
        $this->info('🔧 Initialisation des rôles et permissions (Ultra-Optimisé)...');

        // Reset cached roles and permissions
        // app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Vérifier si data existe déjà
        if (! $this->option('force') && Role::count() > 0) {
            $this->warn('⚠️  Les rôles existent déjà. Utiliser --force pour les recréer');

            return;
        }

        // Nettoyage complet avant l'insertion
        if ($this->option('force') || app()->environment('testing')) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            Schema::disableForeignKeyConstraints();
            DB::table('model_has_roles')->truncate();
            DB::table('model_has_permissions')->truncate();
            DB::table('role_has_permissions')->truncate();
            Role::truncate();
            Permission::truncate();
            Schema::enableForeignKeyConstraints();

            $this->line('🗑️  Rôles et permissions supprimés');
        }

        // ===============================================
        // DÉFINITION DES PERMISSIONS
        // ===============================================
        $permissionNames = [
            'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
            'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
            'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete', 'contrats.print',
            'loyers.view', 'loyers.generate', 'loyers.edit', 'loyers.delete', 'loyers.quittance',
            'revisions.view', 'revisions.create',
            'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete',
            'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
            'proprietaires.view', 'proprietaires.create', 'proprietaires.edit', 'proprietaires.delete', 'proprietaires.bilan',
            'rapports.view', 'rapports.export', 'rapports.mensuel',
            'documents.view', 'documents.upload', 'documents.delete',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'settings.view', 'settings.edit', 'roles.manage',
        ];

        // Bulk insert permissions
        $now = now();
        $permissionData = array_map(fn ($name) => [
            'name' => $name,
            'guard_name' => 'web',
            'created_at' => $now,
            'updated_at' => $now,
        ], $permissionNames);
        DB::table('permissions')->insert($permissionData);

        // Map permission names to IDs for fast lookup
        $permissionMap = DB::table('permissions')->pluck('id', 'name')->toArray();

        // ===============================================
        // DÉFINITION DES RÔLES
        // ===============================================
        $rolesMatrix = [
            'admin' => $permissionNames,
            'direction' => [
                'biens.view', 'locataires.view', 'contrats.view', 'contrats.print', 'loyers.view',
                'revisions.view', 'paiements.view', 'depenses.view',
                'rapports.view', 'rapports.export', 'rapports.mensuel',
                'documents.view', 'proprietaires.view', 'proprietaires.bilan',
            ],
            'gestionnaire' => [
                'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
                'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
                'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete', 'contrats.print',
                'loyers.view', 'loyers.generate', 'loyers.edit', 'loyers.delete',
                'revisions.view', 'revisions.create',
                'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
                'proprietaires.view', 'proprietaires.create', 'proprietaires.edit', 'proprietaires.bilan',
                'documents.view', 'documents.upload',
                'rapports.view', 'rapports.export',
                'paiements.view',
            ],
            'comptable' => [
                'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete',
                'depenses.view', 'depenses.edit',
                'loyers.view', 'loyers.quittance',
                'rapports.view', 'rapports.export', 'rapports.mensuel',
                'biens.view', 'locataires.view', 'contrats.view',
                'proprietaires.view', 'proprietaires.bilan', 'documents.view',
            ],
            'proprietaire' => [
                'biens.view', 'locataires.view', 'contrats.view', 'loyers.view',
                'paiements.view', 'depenses.view', 'documents.view',
                'rapports.view', 'proprietaires.view',
            ],
        ];

        $rolePivotData = [];
        foreach ($rolesMatrix as $roleName => $perms) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleName,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($perms as $pName) {
                if (isset($permissionMap[$pName])) {
                    $rolePivotData[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionMap[$pName],
                    ];
                }
            }
        }

        // Bulk insert pivot data
        DB::table('role_has_permissions')->insert($rolePivotData);

        // User sync (skipped in tests)
        if (! app()->environment('testing')) {
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                if ($user->role) {
                    $roleId = DB::table('roles')->where('name', $user->role)->value('id');
                    if ($roleId) {
                        DB::table('model_has_roles')->insertOrIgnore([
                            'role_id' => $roleId,
                            'model_type' => \App\Models\User::class,
                            'model_id' => $user->id,
                        ]);
                    }
                }
            }
        }

        // Re-cache permissions
        // app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('✨ Rôles et permissions initialisés avec succès !');
    }
}
