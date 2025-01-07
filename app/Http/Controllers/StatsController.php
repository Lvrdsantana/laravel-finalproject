<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\Classes;
use App\Models\Attendance;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour la gestion des statistiques
 * 
 * Ce contrôleur gère l'affichage et le calcul des statistiques :
 * - Taux de présence par étudiant
 * - Taux de présence par classe
 * - Volume horaire par cours
 */
class StatsController extends Controller
{
    /**
     * Affiche la page d'accueil des statistiques
     */
    public function index()
    {
        return view('stats.index');
    }

    /**
     * Affiche la page des statistiques de présence par étudiant
     */
    public function studentAttendance()
    {
        return view('stats.student-attendance');
    }

    /**
     * Affiche la page des statistiques de présence par classe
     */
    public function classAttendance()
    {
        return view('stats.class-attendance');
    }

    /**
     * Affiche la page des statistiques de volume horaire par cours
     */
    public function courseVolume()
    {
        return view('stats.course-volume');
    }

    /**
     * Calcule et retourne les statistiques de présence pour chaque étudiant
     * 
     * @return \Illuminate\Http\JsonResponse Données au format JSON avec pour chaque étudiant :
     * - Nom de l'étudiant
     * - Taux de présence
     * - Couleur selon le taux (vert foncé > 70%, vert clair > 50%, orange > 30%, rouge <= 30%)
     */
    public function getStudentAttendanceData()
    {
        // Récupère tous les étudiants avec leurs présences et données utilisateur
        $students = Students::with(['attendances', 'user'])->get();
        $data = $students->map(function ($student) {
            $total = $student->attendances->count();
            $present = $student->attendances->where('status', 'present')->count();
            $rate = $total > 0 ? ($present / $total) * 100 : 0;
            
            // Définir la couleur selon le taux
            $color = match(true) {
                $rate >= 70 => '#2E7D32', // Vert foncé
                $rate >= 50.1 => '#4CAF50', // Vert clair
                $rate >= 30.1 => '#FF9800', // Orange
                default => '#F44336', // Rouge
            };

            return [
                'name' => $student->user->name,
                'rate' => round($rate, 1),
                'color' => $color
            ];
        })->sortByDesc('rate')->values();

        return response()->json($data);
    }

    /**
     * Calcule et retourne les statistiques de présence pour chaque classe
     * 
     * @return \Illuminate\Http\JsonResponse Données au format JSON avec pour chaque classe :
     * - Nom de la classe
     * - Taux de présence moyen des étudiants
     */
    public function getClassAttendanceData()
    {
        // Récupère toutes les classes avec leurs étudiants et présences
        $classes = Classes::with('students.attendances')->get();
        $data = $classes->map(function ($class) {
            $totalAttendances = 0;
            $totalPresent = 0;

            // Calcul du total des présences pour tous les étudiants de la classe
            foreach ($class->students as $student) {
                $totalAttendances += $student->attendances->count();
                $totalPresent += $student->attendances->where('status', 'present')->count();
            }

            $rate = $totalAttendances > 0 ? ($totalPresent / $totalAttendances) * 100 : 0;

            return [
                'name' => $class->name,
                'rate' => round($rate, 1)
            ];
        });

        return response()->json($data);
    }

    /**
     * Calcule et retourne les statistiques de volume horaire pour chaque cours
     * 
     * @return \Illuminate\Http\JsonResponse Données au format JSON avec pour chaque cours :
     * - Nom du cours
     * - Nombre total de sessions
     */
    public function getCourseVolumeData()
    {
        // Compte le nombre de sessions par cours dans l'emploi du temps
        $courseVolumes = Timetable::with('course')
            ->select('course_id', DB::raw('count(*) as total_sessions'))
            ->groupBy('course_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->course->name,
                    'sessions' => $item->total_sessions
                ];
            });

        return response()->json($courseVolumes);
    }
} 