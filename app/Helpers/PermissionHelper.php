<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Vérifier si l'utilisateur a une permission
     */
    public static function can(string $permission): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Admin a tous les droits
        if ($user->role === 'admin') {
            return true;
        }

        return self::hasPermission($user, $permission);
    }

    /**
     * Matrice de permissions par rôle (Meilleures pratiques immobilières)
     */
    private static function hasPermission($user, string $permission): bool
    {
        $permissions = [
            'gestionnaire' => [
                // Gestion complète du patrimoine
                'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
                'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
                'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete',

                // Gestion locative
                'loyers.view', 'loyers.generate',

                // Rapports opérationnels
                'rapports.view', 'rapports.export',

                // Dépenses
                'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
            ],

            'comptable' => [
                // Finance et comptabilité
                'loyers.view',
                'paiements.view', 'paiements.create', 'paiements.edit',

                // Rapports financiers
                'rapports.view', 'rapports.export',

                // Lecture seule sur le reste (pour contexte)
                'biens.view',
                'locataires.view',
                'contrats.view',
                'depenses.view',
            ],

            'direction' => [
                // Lecture seule sur tout (vision globale)
                'biens.view',
                'locataires.view',
                'contrats.view',
                'loyers.view',
                'paiements.view',
                'depenses.view',

                // Rapports stratégiques complets
                'rapports.view', 'rapports.export',
            ],
        ];

        return in_array($permission, $permissions[$user->role] ?? []);
    }

    /**
     * Obtenir toutes les permissions d'un rôle
     */
    public static function getRolePermissions(string $role): array
    {
        $allPermissions = [
            'admin' => [
                'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
                'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
                'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete',
                'loyers.view', 'loyers.generate',
                'paiements.view', 'paiements.create', 'paiements.edit',
                'rapports.view', 'rapports.export',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'logs.view',
            ],

            'gestionnaire' => [
                'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
                'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
                'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete',
                'loyers.view', 'loyers.generate',
                'rapports.view', 'rapports.export',
            ],

            'comptable' => [
                'loyers.view',
                'paiements.view', 'paiements.create', 'paiements.edit',
                'rapports.view', 'rapports.export',
                'biens.view', 'locataires.view', 'contrats.view',
            ],

            'direction' => [
                'biens.view', 'locataires.view', 'contrats.view',
                'loyers.view', 'paiements.view',
                'rapports.view', 'rapports.export',
            ],
        ];

        return $allPermissions[$role] ?? [];
    }

    /**
     * Obtenir la description d'un rôle
     */
    public static function getRoleDescription(string $role): string
    {
        $descriptions = [
            'admin' => 'Administrateur Système - Accès complet à toutes les fonctionnalités',
            'gestionnaire' => 'Gestionnaire Immobilier - Gestion opérationnelle du patrimoine et des locataires',
            'comptable' => 'Comptable - Gestion financière, encaissements et rapports comptables',
            'direction' => 'Direction - Vision stratégique et rapports de performance (lecture seule)',
        ];

        return $descriptions[$role] ?? 'Rôle inconnu';
    }
}
