<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour ajouter des en-têtes de sécurité à la réponse
 * 
 * Ce middleware ajoute des en-têtes de sécurité à la réponse HTTP
 * pour protéger contre les attaques XSS, clickjacking et MIME-type sniffing.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Protection XSS
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Protection contre le clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Protection contre le MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Politique de sécurité du contenu
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data:;");
        
        // Référer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        
        // HSTS (forcer HTTPS)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
} 