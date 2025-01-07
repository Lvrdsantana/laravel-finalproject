<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour vérifier le rôle coordinateur
 * 
 * Ce middleware vérifie que l'utilisateur connecté a bien le rôle 'coordinators'
 * avant de lui donner accès aux routes protégées.
 */
class CheckCoordinatorRole
{
    /**
     * Gère la requête entrante
     * 
     * @param Request $request La requête HTTP entrante
     * @param Closure $next La fonction suivante dans la chaîne de middleware
     * @return mixed Redirige vers login si non autorisé, sinon passe au middleware suivant
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté et a le rôle coordinateur
        if (!$request->user() || $request->user()->role !== 'coordinators') {
            // Si non autorisé, redirige vers la page de login avec message d'erreur
            return redirect('/login')->with('error', 'Accès non autorisé. Vous devez être coordinateur.');
        }

        // Si autorisé, passe au middleware suivant
        return $next($request);
    }
} 