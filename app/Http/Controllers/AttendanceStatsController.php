<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\courses;
use App\Models\Classes;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\StudentDroppedNotification;
use App\Models\User;

class AttendanceStatsController extends Controller
{
    /**
     * Affiche le tableau de bord des statistiques pour un étudiant
     */
    public function studentDashboard(Request $request, Students $student)
    {
        $courseId = $request->input('course_id');
        $period = $request->input('period', 'semester');
        $stats = $student->getAttendanceStats($courseId, $period);

        $courses = courses::all(); // Pour le menu déroulant des cours

        return view('attendance.student-dashboard', compact('student', 'stats', 'courses', 'period'));
    }

    /**
     * Affiche les statistiques détaillées pour une classe
     */
    public function classDashboard(Request $request, Classes $class)
    {
        $period = $request->input('period', 'semester');
        $now = Carbon::now();
        
        // Calculer les dates selon la période
        switch ($period) {
            case 'week':
                $startDate = $now->startOfWeek();
                $endDate = $now->endOfWeek();
                break;
            case 'month':
                $startDate = $now->startOfMonth();
                $endDate = $now->endOfMonth();
                break;
            case 'semester':
                $startDate = $now->startOfYear();
                $endDate = $now->copy()->addMonths(6);
                break;
            case 'year':
                $startDate = $now->startOfYear();
                $endDate = $now->endOfYear();
                break;
            default:
                $startDate = null;
                $endDate = null;
        }

        // Récupérer les statistiques pour chaque étudiant de la classe
        $studentStats = [];
        foreach ($class->students as $student) {
            $studentStats[$student->id] = [
                'student' => $student,
                'stats' => $student->getAttendanceStats(null, $period)
            ];
        }

        // Calculer la moyenne de la classe
        $classAverage = $class->getClassAttendanceRate($startDate, $endDate);

        return view('attendance.class-dashboard', compact('class', 'studentStats', 'classAverage', 'period'));
    }

    /**
     * Affiche les étudiants droppés
     */
    public function droppedStudents(Request $request)
    {
        $courseId = $request->input('course_id');
        $droppedStudents = [];

        if ($courseId) {
            $course = courses::findOrFail($courseId);
            $students = Students::all();

            foreach ($students as $student) {
                if ($student->checkDropStatus($courseId)) {
                    $droppedStudents[] = [
                        'student' => $student,
                        'attendance_rate' => $student->getAttendanceRate(null, null, $courseId)
                    ];
                }
            }
        }

        $courses = courses::all();
        return view('attendance.dropped-students', compact('droppedStudents', 'courses', 'courseId'));
    }

    /**
     * Exporte les statistiques au format PDF ou Excel
     */
    public function exportStats(Request $request)
    {
        $type = $request->input('type', 'pdf');
        $classId = $request->input('class_id');
        $period = $request->input('period', 'semester');

        $class = Classes::findOrFail($classId);
        $stats = [];

        foreach ($class->students as $student) {
            $stats[] = [
                'student_name' => $student->user->name,
                'attendance_rate' => $student->getAttendanceRate(null, null, null),
                'courses' => courses::all()->map(function ($course) use ($student) {
                    return [
                        'course_name' => $course->name,
                        'grade' => $student->calculateAttendanceGrade($course->id),
                        'is_dropped' => $student->checkDropStatus($course->id)
                    ];
                })
            ];
        }

        if ($type === 'pdf') {
            // Générer PDF avec les statistiques
            $pdf = PDF::loadView('attendance.export.pdf', compact('stats', 'class', 'period'));
            return $pdf->download('attendance_stats.pdf');
        } else {
            // Générer Excel avec les statistiques
            return Excel::download(new AttendanceStatsExport($stats), 'attendance_stats.xlsx');
        }
    }

    protected function notifyCoordinators($student, $course, $attendanceRate)
    {
        try {
            // Récupérer tous les coordinateurs
            $coordinators = User::where('role', 'coordinators')->get();
            
            \Log::info('Found coordinators', [
                'count' => $coordinators->count(),
                'coordinators' => $coordinators->pluck('id')
            ]);

            foreach ($coordinators as $coordinator) {
                try {
                    $notificationData = [
                        'student_id' => $student->id,
                        'student_name' => $student->user->name,
                        'course_id' => $course->id,
                        'course_name' => $course->name,
                        'attendance_rate' => $attendanceRate,
                        'type' => 'student_dropped'
                    ];

                    \Log::info('Creating notification for coordinator', [
                        'coordinator_id' => $coordinator->id,
                        'notification_data' => $notificationData
                    ]);

                    $coordinator->notify(new StudentDroppedNotification($notificationData));

                    \Log::info('Successfully notified coordinator', [
                        'coordinator_id' => $coordinator->id
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to notify individual coordinator', [
                        'coordinator_id' => $coordinator->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error in notifyCoordinators', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
} 