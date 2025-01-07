<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCoordinatorRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'coordinators') {
            return redirect('/login')->with('error', 'Accès non autorisé. Vous devez être coordinateur.');
        }

        return $next($request);
    }
} 