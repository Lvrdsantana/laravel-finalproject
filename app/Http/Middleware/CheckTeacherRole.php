<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour vérifier le rôle enseignant
 * 
 * Ce middleware vérifie que l'utilisateur connecté a bien le rôle 'teachers'
 * avant de lui donner accès aux routes protégées.
 */
class CheckTeacherRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'teachers') {
            return redirect('/login')->with('error', 'Accès non autorisé. Vous devez être enseignant.');
        }

        return $next($request);
    }
} 