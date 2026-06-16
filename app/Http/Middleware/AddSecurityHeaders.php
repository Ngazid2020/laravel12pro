<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Empêche l'intégration dans des iframes (clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Empêche le navigateur de deviner le type MIME (MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Contrôle les informations de référent envoyées
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Désactive les fonctionnalités navigateur non utilisées
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Pour les réponses API : ne pas mettre en cache les données sensibles
        if ($request->is('api/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}