<?php

namespace App\Http\Controllers;

use App\Traits\SecureRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Timetable;

class AuthController extends Controller
{
    use SecureRequests;

    protected $maxLoginAttempts = 5;
    protected $lockoutTime = 300; // 5 minutes

    /**
     * Affiche le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Gère la tentative de connexion de l'utilisateur
     * Redirige vers le tableau de bord approprié selon le rôle
     */
    public function login(Request $request)
    {
        try {
            // Validation des données (sans nettoyer le mot de passe)
            $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|string'
            ]);
            
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];

            // Vérification des tentatives de connexion
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                return redirect()->route('login')
                    ->withErrors(['email' => 'Too many login attempts. Please try again in ' . ceil($this->lockoutTime/60) . ' minutes.']);
            }

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Log de la connexion réussie
                Log::info('Successful login', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip()
                ]);

                if ($user->role == 'coordinators') {
                    return redirect()->route('dashboard');
                } elseif ($user->role == 'students') {
                    return redirect()->route('studentDashboard');
                } elseif ($user->role == 'teachers') {
                    return redirect()->route('teacher.dashboard');
                } elseif ($user->role == 'parents') {
                    return redirect()->route('parent.dashboard');
                }
            }

            // Incrémentation des tentatives de connexion
            $this->incrementLoginAttempts($request);

            // Log de la tentative échouée
            Log::warning('Failed login attempt', [
                'email' => $credentials['email'],
                'ip' => $request->ip()
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'Les informations de connexion sont incorrectes.'])
                ->withInput($request->except('password'));

        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'Une erreur est survenue lors de la connexion.'])
                ->withInput($request->except('password'));
        }
    }

    /**
     * Déconnecte l'utilisateur et invalide sa session
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Affiche le tableau de bord de l'étudiant avec son emploi du temps
     */
    public function studentDashboard()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $timetables = Timetable::with(['class', 'course', 'teacher.user', 'timeSlot'])
            ->whereHas('class', function($query) use ($student) {
                $query->where('id', $student->class_id);
            })
            ->get();
        
        return view('StudentDashboard', compact('timetables'));
    }

    /**
     * Affiche le tableau de bord du professeur avec son emploi du temps
     */
    public function teacherDashboard()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $timetables = Timetable::with(['class', 'course', 'timeSlot'])
            ->where('teacher_id', $teacher->id)
            ->get();
        
        return view('TeacherDashboard', compact('timetables'));
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);
        $attempts = cache()->get($key, 0);
        return $attempts >= $this->maxLoginAttempts;
    }

    protected function incrementLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);
        $attempts = cache()->get($key, 0);
        cache()->put($key, $attempts + 1, $this->lockoutTime);
    }

    protected function throttleKey(Request $request)
    {
        return 'login_attempts_' . $request->ip();
    }

    protected function fireLockoutEvent(Request $request)
    {
        Log::warning('Account locked due to too many login attempts', [
            'ip' => $request->ip(),
            'email' => $request->input('email')
        ]);
    }
}