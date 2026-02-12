<?php

namespace App\Helpers;

use Spatie\Permission\Models\Role;

class PermissionHelper
{
    /**
     * Vérifier si l'utilisateur a une permission
     * Utilise le système de permissions Spatie (base de données)
     */
    public static function can(string $permission): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Admin a tous les droits (Super Admin)
        if ($user->role === 'admin' || ($user->hasRole('admin'))) {
            return true;
        }

        return $user->can($permission);
    }

    /**
     * Obtenir toutes les permissions d'un rôle (Depuis la DB)
     */
    public static function getRolePermissions(string $roleName): array
    {
        $role = Role::findByName($roleName);

        return $role ? $role->permissions->pluck('name')->toArray() : [];
    }

    /**
     * Obtenir la description d'un rôle
     */
    public static function getRoleDescription(string $role): string
    {
        $descriptions = [
            'admin' => 'Administrateur Système - Accès complet à toutes les fonctionnalités',
            'gestionnaire' => 'Gestionnaire Immobilier - Gestion opérationnelle (Biens, Contrats, Loyers)',
            'comptable' => 'Comptable - Gestion financière (Paiements, Dépenses)',
            'direction' => 'Direction - Vision stratégique et rapports (Lecture seule)',
        ];

        return $descriptions[$role] ?? 'Rôle inconnu';
    }
}
