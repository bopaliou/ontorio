<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Support pipe-separated roles in middleware definition, e.g. role:admin|gestionnaire
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode('|', $role));
        }

        $allowedRoles = array_values(array_unique(array_filter($allowedRoles)));

        // Primary source of truth: Spatie roles
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($allowedRoles)) {
            return $next($request);
        }

        // Backward compatibility fallback: legacy users.role field
        if (in_array($user->role, $allowedRoles, true)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access');
    }
}
