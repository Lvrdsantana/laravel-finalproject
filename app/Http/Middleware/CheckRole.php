<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour vérifier le rôle de l'utilisateur
 * 
 * Ce middleware vérifie que l'utilisateur connecté a bien le rôle spécifié
 * avant de lui donner accès aux routes protégées.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || $request->user()->role !== $role) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
} 