<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleValidation
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Vérification du rôle
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        // Vérification de l'état du compte
        if (!$user->is_active) {
            Auth::logout();
            return redirect('login')->with('error', 'Your account has been deactivated.');
        }

        // Vérification de la dernière activité
        if ($user->last_activity && now()->diffInMinutes($user->last_activity) > 30) {
            Auth::logout();
            return redirect('login')->with('error', 'Session expired due to inactivity.');
        }

        // Mise à jour de la dernière activité
        $user->update(['last_activity' => now()]);

        return $next($request);
    }
} 