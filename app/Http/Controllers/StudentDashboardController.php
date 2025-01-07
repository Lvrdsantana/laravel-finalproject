<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Students; 
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Days;
use App\Models\TimeSlots;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur pour le tableau de bord des étudiants
 * 
 * Ce contrôleur gère :
 * - L'affichage du tableau de bord principal
 * - L'affichage de l'emploi du temps
 * - L'affichage et la gestion du profil étudiant
 */
class StudentDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal de l'étudiant
     * avec son emploi du temps
     * 
     * @return \Illuminate\View\View Vue du tableau de bord
     */
    public function index()
    {
        // Récupère l'étudiant connecté
        $student = Auth::user()->student;

        // Récupère l'emploi du temps de sa classe avec les relations
        $timetables = Timetable::with(['class', 'course', 'teacher.user', 'timeSlot'])
            ->whereHas('class', function($query) use ($student) {
                $query->where('id', $student->class_id);
            })
            ->get();

        return view('StudentDashboard', compact('timetables'));
    }

    /**
     * Affiche l'emploi du temps détaillé de l'étudiant
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse Vue de l'emploi du temps ou redirection
     */
    public function timetable()
    {
        // Vérifie que l'utilisateur est bien un étudiant
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        // Récupère l'emploi du temps complet avec toutes les relations
        $timetables = Timetable::with(['class', 'course', 'teacher.user', 'timeSlot'])
            ->whereHas('class', function($query) use ($student) {
                $query->where('id', $student->class_id);
            })
            ->get();

        // Récupère les jours et créneaux horaires pour l'affichage
        $days = \App\Models\Days::all();
        $time_slots = \App\Models\TimeSlots::all();

        return view('student.timetable', compact('timetables', 'days', 'time_slots'));
    }

    /**
     * Affiche le profil de l'étudiant connecté
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse Vue du profil ou redirection
     */
    public function profile()
    {
        // Vérifie que l'utilisateur est bien un étudiant
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        return view('student.profile', compact('student'));
    }
}