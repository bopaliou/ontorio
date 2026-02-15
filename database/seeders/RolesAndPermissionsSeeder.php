<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Biens
            'biens.view',
            'biens.create',
            'biens.edit',
            'biens.delete',
            
            // Propriétaires
            'proprietaires.view',
            'proprietaires.create',
            'proprietaires.edit',
            'proprietaires.delete',
            'proprietaires.bilan',

            // Locataires
            'locataires.view',
            'locataires.create',
            'locataires.edit',
            'locataires.delete',

            // Contrats
            'contrats.view',
            'contrats.create',
            'contrats.edit',
            'contrats.delete',
            'contrats.print',

            // Loyers
            'loyers.view',
            'loyers.create',
            'loyers.edit',
            'loyers.delete',
            'loyers.generate',
            'loyers.quittance',

            // Paiements
            'paiements.view',
            'paiements.create',
            'paiements.edit',
            'paiements.delete',

            // Dépenses
            'depenses.view',
            'depenses.create',
            'depenses.edit',
            'depenses.delete',

            // Documents
            'documents.view',
            'documents.upload',
            'documents.delete',

            // Rapports
            'rapports.view',
            'rapports.mensuel',

            // Relances
            'relances.view',
            'relances.create',
            'relances.edit',
            'relances.delete',

            // Administration
            'utilisateurs.view',
            'utilisateurs.create',
            'utilisateurs.edit',
            'utilisateurs.delete',
            'logs.view',
            'parametres.view',
            'parametres.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // ADMIN
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        // GESTIONNAIRE
        $role = Role::firstOrCreate(['name' => 'gestionnaire']);
        $role->givePermissionTo([
            'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
            'proprietaires.view', 'proprietaires.create', 'proprietaires.edit', 'proprietaires.delete', 'proprietaires.bilan',
            'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
            'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete', 'contrats.print',
            'loyers.view', 'loyers.create', 'loyers.edit', 'loyers.delete', 'loyers.generate', 'loyers.quittance',
            'paiements.view',
            'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
            'documents.view', 'documents.upload', 'documents.delete',
            'rapports.view', 'rapports.mensuel',
            'relances.view', 'relances.create', 'relances.edit'
        ]);

        // COMPTABLE
        $role = Role::firstOrCreate(['name' => 'comptable']);
        $role->givePermissionTo([
            'biens.view',
            'proprietaires.view', 'proprietaires.bilan',
            'locataires.view',
            'contrats.view', 'contrats.print',
            'loyers.view', 'loyers.edit', 'loyers.quittance',
            'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete',
            'depenses.view', 'depenses.create', 'depenses.edit',
            'documents.view',
            'rapports.view', 'rapports.mensuel',
            'relances.view'
        ]);

        // DIRECTION
        $role = Role::firstOrCreate(['name' => 'direction']);
        $role->givePermissionTo([
            'biens.view',
            'proprietaires.view', 'proprietaires.bilan',
            'locataires.view',
            'contrats.view', 'contrats.print',
            'loyers.view', 'loyers.quittance',
            'paiements.view',
            'depenses.view',
            'documents.view',
            'rapports.view', 'rapports.mensuel',
            'relances.view',
            'logs.view',
            'parametres.view'
        ]);

        // PROPRIETAIRE
        $role = Role::firstOrCreate(['name' => 'proprietaire']);
        // Usually handled via policies/scopes, but specific permissions might be needed?
        // For now, no explicit permissions beyond login/view dashboard sections allowed by role check.

        // LOCATAIRE
        $role = Role::firstOrCreate(['name' => 'locataire']);
    }
}
