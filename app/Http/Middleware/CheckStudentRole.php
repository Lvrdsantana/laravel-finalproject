<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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