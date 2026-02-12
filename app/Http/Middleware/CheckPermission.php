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

        // Super Admin, Spatie permission, or legacy permission matrix
        $hasAccess = $user->role === 'admin'
            || (method_exists($user, 'can') && $user->can($permission))
            || $this->hasLegacyPermission($user, $permission);

        if ($hasAccess) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé');
    }

    /**
     * Legacy permission matrix (temporary fallback during migration to Spatie).
     */
    private function hasLegacyPermission($user, string $permission): bool
    {
        $permissions = [
            'gestionnaire' => [
                'biens.view', 'biens.create', 'biens.edit', 'biens.delete',
                'locataires.view', 'locataires.create', 'locataires.edit', 'locataires.delete',
                'contrats.view', 'contrats.create', 'contrats.edit', 'contrats.delete', 'contrats.print',
                'loyers.view', 'loyers.generate',
                'paiements.view', 'paiements.create', 'paiements.edit',
                'depenses.view', 'depenses.create', 'depenses.edit', 'depenses.delete',
                'proprietaires.view', 'proprietaires.create', 'proprietaires.edit', 'proprietaires.delete', 'proprietaires.bilan',
                'documents.view', 'documents.upload', 'documents.delete',
                'rapports.view', 'rapports.export', 'rapports.mensuel',
            ],
            'comptable' => [
                'biens.view',
                'locataires.view',
                'contrats.view',
                'loyers.view', 'loyers.generate', 'loyers.quittance',
                'paiements.view', 'paiements.create', 'paiements.edit', 'paiements.delete',
                'depenses.view', 'depenses.create', 'depenses.edit',
                'proprietaires.view', 'proprietaires.bilan',
                'documents.view',
                'rapports.view', 'rapports.export', 'rapports.mensuel',
            ],
            'direction' => [
                'biens.view',
                'locataires.view',
                'contrats.view',
                'loyers.view',
                'paiements.view',
                'depenses.view',
                'proprietaires.view', 'proprietaires.bilan',
                'documents.view',
                'rapports.view', 'rapports.export', 'rapports.mensuel',
            ],
        ];

        return in_array($permission, $permissions[$user->role] ?? [], true);
    }
}
