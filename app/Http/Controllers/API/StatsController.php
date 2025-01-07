<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StudentPresence;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function studentStats(Request $request, $student_id = null)
    {
        // Si student_id n'est pas fourni et que l'utilisateur est un étudiant
        if (!$student_id && $request->user()->role === 'student') {
            $student_id = $request->user()->id;
        }

        // Vérifier les permissions
        if ($request->user()->role === 'student' && $student_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $presences = StudentPresence::where('student_id', $student_id)
            ->with(['timetable.course'])
            ->get();

        $totalCourses = $presences->count();
        $absences = $presences->where('status', 'absent')->count();
        $justified = $presences->where('status', 'absent')->where('justified', true)->count();
        $lates = $presences->where('status', 'late')->count();

        // Statistiques par matière
        $courseStats = $presences
            ->groupBy('timetable.course.id')
            ->map(function ($coursePresences) {
                $total = $coursePresences->count();
                $absences = $coursePresences->where('status', 'absent')->count();
                $lates = $coursePresences->where('status', 'late')->count();
                
                return [
                    'course_name' => $coursePresences->first()->timetable->course->name,
                    'total' => $total,
                    'absences' => $absences,
                    'lates' => $lates,
                    'absence_rate' => $total > 0 ? round(($absences / $total) * 100, 2) : 0,
                ];
            })->values();

        return response()->json([
            'global_stats' => [
                'total_courses' => $totalCourses,
                'total_absences' => $absences,
                'justified_absences' => $justified,
                'total_lates' => $lates,
                'absence_rate' => $totalCourses > 0 ? round(($absences / $totalCourses) * 100, 2) : 0,
            ],
            'course_stats' => $courseStats,
            'monthly_stats' => $this->getMonthlyStats($presences),
        ]);
    }

    public function classStats(Request $request, $class_id)
    {
        $class = ClassRoom::findOrFail($class_id);
        
        // Récupérer tous les étudiants de la classe
        $students = User::where('role', 'student')
            ->where('class_id', $class_id)
            ->with(['presences.timetable.course'])
            ->get();

        $globalStats = [
            'total_students' => $students->count(),
            'average_absence_rate' => 0,
            'most_absent_course' => null,
        ];

        $studentStats = $students->map(function ($student) {
            $presences = $student->presences;
            $total = $presences->count();
            $absences = $presences->where('status', 'absent')->count();
            
            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'total_courses' => $total,
                'absences' => $absences,
                'absence_rate' => $total > 0 ? round(($absences / $total) * 100, 2) : 0,
            ];
        })->sortByDesc('absence_rate')->values();

        // Statistiques par cours
        $courseStats = Course::whereHas('timetables', function ($query) use ($class_id) {
            $query->where('class_id', $class_id);
        })->get()->map(function ($course) use ($class_id) {
            $presences = StudentPresence::whereHas('timetable', function ($query) use ($course, $class_id) {
                $query->where('course_id', $course->id)
                    ->where('class_id', $class_id);
            })->get();

            $total = $presences->count();
            $absences = $presences->where('status', 'absent')->count();
            
            return [
                'course_id' => $course->id,
                'course_name' => $course->name,
                'total_presences' => $total,
                'total_absences' => $absences,
                'absence_rate' => $total > 0 ? round(($absences / $total) * 100, 2) : 0,
            ];
        })->sortByDesc('absence_rate')->values();

        // Calculer le taux d'absence moyen
        $globalStats['average_absence_rate'] = $studentStats->avg('absence_rate');
        $globalStats['most_absent_course'] = $courseStats->first();

        return response()->json([
            'global_stats' => $globalStats,
            'student_stats' => $studentStats,
            'course_stats' => $courseStats,
        ]);
    }

    public function teacherStats(Request $request, $teacher_id = null)
    {
        // Si teacher_id n'est pas fourni et que l'utilisateur est un enseignant
        if (!$teacher_id && $request->user()->role === 'teacher') {
            $teacher_id = $request->user()->id;
        }

        // Vérifier les permissions
        if ($request->user()->role === 'teacher' && $teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $timetables = DB::table('timetables')
            ->where('teacher_id', $teacher_id)
            ->join('courses', 'timetables.course_id', '=', 'courses.id')
            ->join('class_rooms', 'timetables.class_id', '=', 'class_rooms.id')
            ->select('timetables.*', 'courses.name as course_name', 'class_rooms.name as class_name')
            ->get();

        $presences = StudentPresence::whereIn('timetable_id', $timetables->pluck('id'))
            ->get();

        $stats = [
            'total_courses' => $timetables->count(),
            'total_students_tracked' => $presences->count(),
            'absence_rate' => $presences->count() > 0 
                ? round(($presences->where('status', 'absent')->count() / $presences->count()) * 100, 2) 
                : 0,
            'classes' => $timetables->groupBy('class_id')->map(function ($classTimetables) use ($presences) {
                $classPresences = $presences->whereIn('timetable_id', $classTimetables->pluck('id'));
                return [
                    'class_name' => $classTimetables->first()->class_name,
                    'total_courses' => $classTimetables->count(),
                    'absence_rate' => $classPresences->count() > 0 
                        ? round(($classPresences->where('status', 'absent')->count() / $classPresences->count()) * 100, 2) 
                        : 0,
                ];
            })->values(),
        ];

        return response()->json($stats);
    }

    private function getMonthlyStats($presences)
    {
        return $presences->groupBy(function ($presence) {
            return Carbon::parse($presence->timetable->date)->format('Y-m');
        })->map(function ($monthPresences) {
            $total = $monthPresences->count();
            $absences = $monthPresences->where('status', 'absent')->count();
            $lates = $monthPresences->where('status', 'late')->count();
            
            return [
                'total' => $total,
                'absences' => $absences,
                'lates' => $lates,
                'absence_rate' => $total > 0 ? round(($absences / $total) * 100, 2) : 0,
            ];
        });
    }
} 