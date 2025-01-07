<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Timetable;

class AuthController extends Controller
{

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
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
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

        // Message d'erreur si les informations de connexion ne sont pas correctes
        return redirect()->route('login')->withErrors(['email' => 'Les informations de connexion sont incorrectes.']);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

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
 
}