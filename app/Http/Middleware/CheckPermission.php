<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super Admin a tous les droits
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Vérifier si l'utilisateur a la permission
        if ($this->hasPermission($user, $permission)) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé - Permission requise: '.$permission);
    }

    /**
     * Vérifier si l'utilisateur a une permission spécifique
     */
    private function hasPermission($user, string $permission): bool
    {
        // Matrice de permissions par rôle (basée sur les meilleures pratiques immobilières)
        $permissions = [
            'gestionnaire' => [
                // Gestion du patrimoine
                'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
                'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
                'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete',

                // Gestion locative
                'loyers.view', 'loyers.generate',

                // Rapports
                'rapports.view', 'rapports.export',
            ],

            'comptable' => [
                // Finance uniquement
                'loyers.view',
                'paiements.view', 'paiements.create', 'paiements.edit',

                // Rapports financiers
                'rapports.view', 'rapports.export',

                // Lecture seule sur le reste
                'biens.view',
                'locataires.view',
                'contrats.view',
            ],

            'direction' => [
                // Lecture seule sur tout
                'biens.view',
                'locataires.view',
                'contrats.view',
                'loyers.view',
                'paiements.view',

                // Rapports complets
                'rapports.view', 'rapports.export',
            ],
        ];

        return in_array($permission, $permissions[$user->role] ?? []);
    }
}
