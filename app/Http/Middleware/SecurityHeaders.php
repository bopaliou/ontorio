<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de sécurité HTTP
 * Ajoute les headers de sécurité recommandés par l'OWASP
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Protection contre le sniffing MIME type
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Protection contre le clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Protection XSS (navigateurs anciens)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer Policy - limiter les informations envoyées
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - désactiver les fonctionnalités non utilisées
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // En production, activer HSTS (HTTPS strict)
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
