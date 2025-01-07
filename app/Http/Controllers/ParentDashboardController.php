<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\Timetable;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function index()
    {
        // Récupérer le parent connecté avec ses étudiants
        $parent = ParentModel::where('user_id', Auth::id())
            ->with(['students.timetables.course', 'students.attendances' => function($query) {
                $query->where('status', '!=', 'present')
                    ->with(['timetable.course', 'timetable.teacher.user', 'justification'])
                    ->orderBy('date', 'desc');
            }])
            ->firstOrFail();

        // Organiser les données pour la vue
        $studentsData = [];
        foreach ($parent->students as $student) {
            // Calculer les statistiques
            $totalAbsences = $student->attendances->where('status', '!=', 'present')->count();
            $totalPresences = $student->attendances->where('status', '=', 'present')->count();
            $attendanceRate = $totalPresences + $totalAbsences > 0 
                ? ($totalPresences / ($totalPresences + $totalAbsences)) * 100 
                : 100;

            // Calculer le nombre de cours cette semaine
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            $weeklyCourses = $student->timetables()
                ->where('day_id', now()->dayOfWeek)
                ->count();

            // Trouver le prochain cours
            $nextCourse = $student->timetables()
                ->whereHas('timeSlot', function($query) {
                    $query->where('start_time', '>', now()->format('H:i:s'));
                })
                ->where('day_id', now()->dayOfWeek)
                ->with(['course', 'timeSlot'])
                ->orderBy('time_slot_id')
                ->first();

            $studentsData[] = [
                'student' => $student,
                'timetables' => $student->timetables()
                    ->with(['course', 'teacher.user', 'timeSlot', 'day'])
                    ->orderBy('day_id')
                    ->orderBy('time_slot_id')
                    ->get(),
                'absences' => [
                    'justified' => $student->attendances
                        ->where('status', '!=', 'present')
                        ->filter(function($attendance) {
                            return $attendance->justification !== null;
                        })
                        ->map(function($attendance) {
                            // Cast des dates
                            if ($attendance->date) {
                                $attendance->date = \Carbon\Carbon::parse($attendance->date);
                            }
                            if ($attendance->justification && $attendance->justification->justified_at) {
                                $attendance->justification->justified_at = \Carbon\Carbon::parse($attendance->justification->justified_at);
                            }
                            return $attendance;
                        }),
                    'unjustified' => $student->attendances
                        ->where('status', '!=', 'present')
                        ->filter(function($attendance) {
                            return $attendance->justification === null;
                        })
                        ->map(function($attendance) {
                            // Cast des dates
                            if ($attendance->created_at) {
                                $attendance->date = \Carbon\Carbon::parse($attendance->created_at);
                            }
                            return $attendance;
                        })
                ],
                'stats' => [
                    'attendance_rate' => round($attendanceRate, 1),
                    'total_absences' => $totalAbsences,
                    'weekly_courses' => $weeklyCourses,
                    'next_course' => $nextCourse ? $nextCourse->course->name : null
                ]
            ];
        }

        return view('parent.dashboard', compact('studentsData'));
    }
} 