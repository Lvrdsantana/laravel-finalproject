<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\Timetable;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur pour le tableau de bord des parents
 * 
 * Ce contrôleur gère l'affichage des informations pour les parents :
 * - Liste des étudiants associés au parent
 * - Emplois du temps des étudiants
 * - Absences justifiées et non justifiées
 * - Statistiques de présence
 */
class ParentDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du parent avec toutes les informations de ses enfants
     * 
     * @return \Illuminate\View\View Vue du tableau de bord parent
     */
    public function index()
    {
        // Récupérer le parent connecté avec ses étudiants et leurs données associées
        $parent = ParentModel::where('user_id', Auth::id())
            ->with(['students.timetables.course', 'students.attendances' => function($query) {
                $query->where('status', '!=', 'present')
                    ->with(['timetable.course', 'timetable.teacher.user', 'justification'])
                    ->orderBy('date', 'desc');
            }])
            ->firstOrFail();

        // Tableau pour stocker les données formatées de chaque étudiant
        $studentsData = [];
        foreach ($parent->students as $student) {
            // Calcul des statistiques de présence
            $totalAbsences = $student->attendances->where('status', '!=', 'present')->count();
            $totalPresences = $student->attendances->where('status', '=', 'present')->count();
            $attendanceRate = $totalPresences + $totalAbsences > 0 
                ? ($totalPresences / ($totalPresences + $totalAbsences)) * 100 
                : 100;

            // Récupération des cours de la semaine en cours
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            $weeklyCourses = $student->timetables()
                ->where('day_id', now()->dayOfWeek)
                ->count();

            // Recherche du prochain cours de la journée
            $nextCourse = $student->timetables()
                ->whereHas('timeSlot', function($query) {
                    $query->where('start_time', '>', now()->format('H:i:s'));
                })
                ->where('day_id', now()->dayOfWeek)
                ->with(['course', 'timeSlot'])
                ->orderBy('time_slot_id')
                ->first();

            // Construction du tableau de données pour chaque étudiant
            $studentsData[] = [
                'student' => $student,
                // Récupération de l'emploi du temps complet
                'timetables' => $student->timetables()
                    ->with(['course', 'teacher.user', 'timeSlot', 'day'])
                    ->orderBy('day_id')
                    ->orderBy('time_slot_id')
                    ->get(),
                'absences' => [
                    // Traitement des absences justifiées
                    'justified' => $student->attendances
                        ->where('status', '!=', 'present')
                        ->filter(function($attendance) {
                            return $attendance->justification !== null;
                        })
                        ->map(function($attendance) {
                            // Conversion des dates en objets Carbon
                            if ($attendance->date) {
                                $attendance->date = \Carbon\Carbon::parse($attendance->date);
                            }
                            if ($attendance->justification && $attendance->justification->justified_at) {
                                $attendance->justification->justified_at = \Carbon\Carbon::parse($attendance->justification->justified_at);
                            }
                            return $attendance;
                        }),
                    // Traitement des absences non justifiées
                    'unjustified' => $student->attendances
                        ->where('status', '!=', 'present')
                        ->filter(function($attendance) {
                            return $attendance->justification === null;
                        })
                        ->map(function($attendance) {
                            // Conversion des dates en objets Carbon
                            if ($attendance->created_at) {
                                $attendance->date = \Carbon\Carbon::parse($attendance->created_at);
                            }
                            return $attendance;
                        })
                ],
                // Statistiques globales de l'étudiant
                'stats' => [
                    'attendance_rate' => round($attendanceRate, 1),
                    'total_absences' => $totalAbsences,
                    'weekly_courses' => $weeklyCourses,
                    'next_course' => $nextCourse ? $nextCourse->course->name : null
                ]
            ];
        }

        // Retourne la vue avec les données formatées
        return view('parent.dashboard', compact('studentsData'));
    }
} 