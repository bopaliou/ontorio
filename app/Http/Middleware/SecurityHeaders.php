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

        // Content Security Policy (CSP) - Défense contre XSS
        // Note: 'unsafe-inline' est utilisé ici car le dashboard utilise des scripts inline pour SPA
        // Dans un environnement idéal, on utiliserait des nonces ou des fichiers séparés.
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; ";
        $csp .= "font-src 'self' https://fonts.gstatic.com; ";
        $csp .= "img-src 'self' data: blob:; ";
        $csp .= "frame-ancestors 'none'; ";
        $csp .= "connect-src 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        // Permissions Policy - désactiver les fonctionnalités non utilisées
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // En production, activer HSTS (HTTPS strict)
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
