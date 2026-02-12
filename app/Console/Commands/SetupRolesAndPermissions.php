<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SetupRolesAndPermissions extends Command
{
    protected $signature = 'app:setup-roles-permissions
                            {--force : Force la recréation même si déjà existantes}';

    protected $description = 'Initialiser les rôles et permissions du système';

    public function handle()
    {
        $this->info('🔧 Initialisation des rôles et permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Vérifier si data existe déjà
        if (! $this->option('force') && Role::count() > 0) {
            $this->warn('⚠️  Les rôles existent déjà. Utiliser --force pour les recréer');

            return;
        }

        // Supprimer ancien data si force
        if ($this->option('force')) {
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
        $permissions = [
            // Module Biens
            'biens.view' => 'Voir les biens immobiliers',
            'biens.create' => 'Créer un bien immobilier',
            'biens.edit' => 'Modifier un bien immobilier',
            'biens.delete' => 'Supprimer un bien immobilier',

            // Module Locataires
            'locataires.view' => 'Voir les locataires',
            'locataires.create' => 'Créer un locataire',
            'locataires.edit' => 'Modifier un locataire',
            'locataires.delete' => 'Supprimer un locataire',

            // Module Contrats
            'contrats.view' => 'Voir les contrats',
            'contrats.create' => 'Créer un contrat',
            'contrats.edit' => 'Modifier un contrat',
            'contrats.delete' => 'Supprimer un contrat',
            'contrats.print' => 'Imprimer un contrat',

            // Module Loyers
            'loyers.view' => 'Voir les loyers',
            'loyers.generate' => 'Générer les loyers du mois',
            'loyers.edit' => 'Modifier un loyer (Correction)',
            'loyers.delete' => 'Supprimer un loyer',
            'loyers.quittance' => 'Générer les quittances',

            // Module Révisions
            'revisions.view' => 'Voir les révisions',
            'revisions.create' => 'Créer une révision',

            // Module Paiements
            'paiements.view' => 'Voir les paiements',
            'paiements.create' => 'Enregistrer un paiement',
            'paiements.edit' => 'Modifier un paiement',
            'paiements.delete' => 'Supprimer un paiement',

            // Module Dépenses
            'depenses.view' => 'Voir les dépenses',
            'depenses.create' => 'Créer une dépense',
            'depenses.edit' => 'Modifier une dépense',
            'depenses.delete' => 'Supprimer une dépense',

            // Module Propriétaires
            'proprietaires.view' => 'Voir les propriétaires',
            'proprietaires.create' => 'Créer un propriétaire',
            'proprietaires.edit' => 'Modifier un propriétaire',
            'proprietaires.delete' => 'Supprimer un propriétaire',
            'proprietaires.bilan' => 'Voir le bilan propriétaire',

            // Module Rapports
            'rapports.view' => 'Voir les rapports',
            'rapports.export' => 'Exporter les rapports',
            'rapports.mensuel' => 'Générer rapport mensuel',

            // Module Documents
            'documents.view' => 'Voir les documents',
            'documents.upload' => 'Téléverser des documents',
            'documents.delete' => 'Supprimer des documents',

            // Module Utilisateurs
            'users.view' => 'Voir les utilisateurs',
            'users.create' => 'Créer un utilisateur',
            'users.edit' => 'Modifier un utilisateur',
            'users.delete' => 'Supprimer un utilisateur',

            // Module Paramètres
            'settings.view' => 'Voir les paramètres',
            'settings.edit' => 'Modifier les paramètres',
            'roles.manage' => 'Gérer les rôles et permissions',
        ];

        // Créer toutes les permissions
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name, 'description' => $description]);
        }
        $this->line('✅ '.count($permissions).' permissions créées');

        // ===============================================
        // DÉFINITION DES RÔLES
        // ===============================================
        // ===============================================
        // DÉFINITION DES RÔLES
        // ===============================================
        $roles = [
            'admin' => [
                'description' => 'Administrateur Système - Accès complet',
                'permissions' => array_keys($permissions), // Tous les droits
            ],
            'direction' => [
                'description' => 'Direction - Vision stratégique (Lecture Seule)',
                'permissions' => [
                    // Lecture Globale Opérationnelle
                    'biens.view',
                    'locataires.view',
                    'contrats.view',
                    'loyers.view',
                    'revisions.view',

                    // Lecture Globale Financière
                    'paiements.view',
                    'depenses.view',

                    // Rapports & Documents
                    'rapports.view', 'rapports.export', 'rapports.mensuel',
                    'documents.view',
                    'proprietaires.view', 'proprietaires.bilan',
                ],
            ],
            'gestionnaire' => [
                'description' => 'Gestionnaire - Opérations (Patrimoine & Locataires)',
                'permissions' => [
                    // BIENS: Full CRUD
                    'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
                    // LOCATAIRES: Full CRUD
                    'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
                    // CONTRATS: Full CRUD + Print
                    'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete', 'contrats.print',
                    // LOYERS: Generation & Gestion
                    'loyers.view', 'loyers.generate', 'loyers.edit', 'loyers.delete', // Peut corriger un loyer généré par erreur
                    // REVISIONS
                    'revisions.view', 'revisions.create',
                    // DEPENSES: Demande (Create) mais pas Paiement
                    'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
                    // PROPRIETAIRES
                    'proprietaires.view', 'proprietaires.create', 'proprietaires.edit', 'proprietaires.bilan',
                    // DOCUMENTS
                    'documents.view', 'documents.upload',
                    // RAPPORTS (Opérationnels)
                    'rapports.view', 'rapports.export',
                    // FINANCE (Lecture seule stricte)
                    'paiements.view',
                ],
            ],
            'comptable' => [
                'description' => 'Comptable - Finance & Trésorerie',
                'permissions' => [
                    // FINANCE: Full CRUD
                    'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete',
                    'depenses.view', 'depenses.edit', // Peut marquer payé

                    // LOYERS: Lecture + Quittances
                    'loyers.view', 'loyers.quittance',

                    // RAPPORTS
                    'rapports.view', 'rapports.export', 'rapports.mensuel',

                    // CONTEXTE OPERATIONNEL (Lecture Seule)
                    'biens.view',
                    'locataires.view',
                    'contrats.view',
                    'proprietaires.view', 'proprietaires.bilan',
                    'documents.view',
                ],
            ],
            'proprietaire' => [
                'description' => 'Propriétaire - Consultation (Biens & Finances)',
                'permissions' => [
                    // Vision limitée à ses propres données (géré par Policy/Scopes, pas Permission)
                    // Mais on donne les droits de "view" génériques
                    'biens.view',
                    'locataires.view',
                    'contrats.view',
                    'loyers.view',
                    'paiements.view',
                    'depenses.view',
                    'documents.view',
                    'rapports.view',
                    'proprietaires.view', // Voir son propre profil
                ],
            ],
        ];

        // Créer les rôles et assigner permissions
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['description' => $roleData['description']]
            );
            $role->syncPermissions($roleData['permissions']);
            $this->line("✅ Rôle '{$roleName}' créé avec ".count($roleData['permissions']).' permissions');
        }

        // ===============================================
        // SYNCHRONISATION UTILISATEURS EXISTANTS
        // ===============================================
        $this->info('🔄 Synchronisation des rôles utilisateurs...');
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if ($user->role && Role::where('name', $user->role)->exists()) {
                $user->assignRole($user->role);
            }
        }
        $this->line('✅ '.count($users).' utilisateurs synchronisés');

        $this->info('✨ Rôles et permissions initialisés et appliqués avec succès !');
    }
}
