<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Rôles pour la gestion immobilière Ontario Group :
     * - admin : Super administrateur, tous les droits
     * - gestionnaire : Gestion du patrimoine (biens, locataires, contrats)
     * - comptable : Gestion financière (paiements, loyers, rapports)
     * - direction : Lecture seule + rapports complets
     * - agent_commercial : Prospection et acquisition locataires
     * - technicien : Maintenance et interventions techniques
     * - proprietaire : Lecture seule sur ses propres biens (futur)
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===============================================
        // DÉFINITION DES PERMISSIONS PAR MODULE
        // ===============================================

        $permissions = [
            // Module Biens
            'biens.view'        => 'Voir les biens immobiliers',
            'biens.create'      => 'Créer un bien immobilier',
            'biens.edit'        => 'Modifier un bien immobilier',
            'biens.delete'      => 'Supprimer un bien immobilier',
            
            // Module Locataires
            'locataires.view'   => 'Voir les locataires',
            'locataires.create' => 'Créer un locataire',
            'locataires.edit'   => 'Modifier un locataire',
            'locataires.delete' => 'Supprimer un locataire',
            
            // Module Contrats
            'contrats.view'     => 'Voir les contrats',
            'contrats.create'   => 'Créer un contrat',
            'contrats.edit'     => 'Modifier un contrat',
            'contrats.delete'   => 'Supprimer un contrat',
            'contrats.print'    => 'Imprimer un contrat',
            
            // Module Loyers
            'loyers.view'       => 'Voir les loyers',
            'loyers.generate'   => 'Générer les loyers du mois',
            'loyers.quittance'  => 'Générer les quittances',
            
            // Module Paiements
            'paiements.view'    => 'Voir les paiements',
            'paiements.create'  => 'Enregistrer un paiement',
            'paiements.edit'    => 'Modifier un paiement',
            'paiements.delete'  => 'Supprimer un paiement',
            
            // Module Dépenses
            'depenses.view'     => 'Voir les dépenses',
            'depenses.create'   => 'Créer une dépense',
            'depenses.edit'     => 'Modifier une dépense',
            'depenses.delete'   => 'Supprimer une dépense',
            
            // Module Propriétaires
            'proprietaires.view'   => 'Voir les propriétaires',
            'proprietaires.create' => 'Créer un propriétaire',
            'proprietaires.edit'   => 'Modifier un propriétaire',
            'proprietaires.delete' => 'Supprimer un propriétaire',
            'proprietaires.bilan'  => 'Voir le bilan propriétaire',
            
            // Module Rapports
            'rapports.view'     => 'Voir les rapports',
            'rapports.export'   => 'Exporter les rapports',
            'rapports.mensuel'  => 'Générer rapport mensuel',
            
            // Module Documents
            'documents.view'    => 'Voir les documents',
            'documents.upload'  => 'Téléverser des documents',
            'documents.delete'  => 'Supprimer des documents',
            
            // Module Utilisateurs
            'users.view'        => 'Voir les utilisateurs',
            'users.create'      => 'Créer un utilisateur',
            'users.edit'        => 'Modifier un utilisateur',
            'users.delete'      => 'Supprimer un utilisateur',
            
            // Module Paramètres
            'settings.view'     => 'Voir les paramètres',
            'settings.edit'     => 'Modifier les paramètres',
            'roles.manage'      => 'Gérer les rôles et permissions',
        ];

        // Créer toutes les permissions
        foreach ($permissions as $name => $description) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        // ===============================================
        // DÉFINITION DES RÔLES ET LEURS PERMISSIONS
        // ===============================================

        // ADMIN - Tous les droits
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // GESTIONNAIRE - Gestion du patrimoine immobilier
        $gestionnaireRole = Role::create(['name' => 'gestionnaire', 'guard_name' => 'web']);
        $gestionnaireRole->givePermissionTo([
            // Biens : Toutes les actions
            'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
            // Locataires : Toutes les actions
            'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
            // Contrats : Toutes les actions
            'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete', 'contrats.print',
            // Loyers : Voir et générer
            'loyers.view', 'loyers.generate', 'loyers.quittance',
            // Propriétaires : Voir et créer
            'proprietaires.view', 'proprietaires.create', 'proprietaires.edit',
            // Documents : Toutes les actions
            'documents.view', 'documents.upload', 'documents.delete',
            // Dépenses : Toutes les actions
            'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
            // Rapports : Lecture
            'rapports.view', 'rapports.export',
        ]);

        // COMPTABLE - Gestion financière
        $comptableRole = Role::create(['name' => 'comptable', 'guard_name' => 'web']);
        $comptableRole->givePermissionTo([
            // Lecture seule sur le patrimoine
            'biens.view',
            'locataires.view',
            'contrats.view',
            'proprietaires.view',
            // Loyers : Voir et quittances
            'loyers.view', 'loyers.quittance',
            // Paiements : Toutes les actions
            'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete',
            // Dépenses : Toutes les actions
            'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
            // Rapports : Toutes les actions
            'rapports.view', 'rapports.export', 'rapports.mensuel',
            // Propriétaires : Bilan
            'proprietaires.bilan',
            // Documents : Lecture
            'documents.view',
        ]);

        // DIRECTION - Lecture seule + Rapports complets
        $directionRole = Role::create(['name' => 'direction', 'guard_name' => 'web']);
        $directionRole->givePermissionTo([
            // Lecture seule sur tout
            'biens.view',
            'locataires.view',
            'contrats.view',
            'loyers.view',
            'paiements.view',
            'depenses.view',
            'proprietaires.view', 'proprietaires.bilan',
            'documents.view',
            // Rapports : Accès complet
            'rapports.view', 'rapports.export', 'rapports.mensuel',
        ]);

        // AGENT COMMERCIAL - Prospection et acquisition locataires
        $agentRole = Role::create(['name' => 'agent_commercial', 'guard_name' => 'web']);
        $agentRole->givePermissionTo([
            // Biens : Lecture seule  
            'biens.view',
            // Locataires : Créer et modifier (prospection)
            'locataires.view', 'locataires.create', 'locataires.edit',
            // Contrats : Lecture seule
            'contrats.view',
            // Documents : Upload pour dossiers locataires
            'documents.view', 'documents.upload',
        ]);

        // TECHNICIEN - Maintenance et interventions
        $technicienRole = Role::create(['name' => 'technicien', 'guard_name' => 'web']);
        $technicienRole->givePermissionTo([
            // Biens : Lecture seule
            'biens.view',
            // Locataires : Lecture seule (pour contacts)
            'locataires.view',
            // Dépenses : Créer (pour frais d'intervention)
            'depenses.view', 'depenses.create',
            // Documents : Upload (photos interventions)
            'documents.view', 'documents.upload',
        ]);

        // PROPRIÉTAIRE (futur) - Lecture seule sur ses propres biens
        $proprietaireRole = Role::create(['name' => 'proprietaire', 'guard_name' => 'web']);
        $proprietaireRole->givePermissionTo([
            // Biens : Lecture seule (filtrée par propriétaire)
            'biens.view',
            // Loyers : Lecture seule
            'loyers.view',
            // Paiements : Lecture seule
            'paiements.view',
            // Rapports : Lecture seule
            'rapports.view',
            // Propriétaires : Son propre bilan
            'proprietaires.bilan',
        ]);

        // ===============================================
        // MIGRATION DES UTILISATEURS EXISTANTS
        // ===============================================
        
        // Assigner les rôles Spatie aux utilisateurs existants basé sur leur champ 'role'
        $users = User::whereNotNull('role')->get();
        
        foreach ($users as $user) {
            $legacyRole = $user->role;
            
            // Mapping des anciens rôles vers les nouveaux
            if (Role::where('name', $legacyRole)->exists()) {
                $user->assignRole($legacyRole);
            } else {
                // Si le rôle n'existe pas, assigner 'gestionnaire' par défaut
                $user->assignRole('gestionnaire');
            }
        }

        $this->command->info('✅ Rôles et permissions créés avec succès !');
        $this->command->info('📊 ' . Permission::count() . ' permissions créées');
        $this->command->info('👥 ' . Role::count() . ' rôles créés');
        $this->command->info('🔄 ' . $users->count() . ' utilisateurs migrés');
    }
}
