<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour vérifier le rôle étudiant
 * 
 * Ce middleware vérifie que l'utilisateur connecté a bien le rôle 'students'
 * avant de lui donner accès aux routes protégées.
 */
class CheckStudentRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'students') {
            return redirect('/login')->with('error', 'Accès non autorisé. Vous devez être étudiant.');
        }

        return $next($request);
    }
} 