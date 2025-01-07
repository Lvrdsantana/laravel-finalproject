<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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