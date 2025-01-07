<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\courses; 
use App\Models\Classes;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\StudentDroppedNotification;
use App\Models\User;

/**
 * Contrôleur pour la gestion des statistiques de présence
 * 
 * Ce contrôleur gère :
 * - L'affichage des statistiques par étudiant
 * - L'affichage des statistiques par classe
 * - Le suivi des étudiants "droppés" (trop d'absences)
 * - L'export des statistiques en PDF/Excel
 * - Les notifications aux coordinateurs
 */
class AttendanceStatsController extends Controller
{
    /**
     * Affiche le tableau de bord des statistiques pour un étudiant spécifique
     * 
     * @param Request $request Requête HTTP contenant les filtres (cours, période)
     * @param Students $student L'étudiant dont on veut voir les stats
     * @return \Illuminate\View\View Vue avec les statistiques de l'étudiant
     */
    public function studentDashboard(Request $request, Students $student)
    {
        // Récupérer les paramètres de filtre
        $courseId = $request->input('course_id');
        $period = $request->input('period', 'semester'); // Période par défaut: semestre
        
        // Calculer les statistiques pour l'étudiant
        $stats = $student->getAttendanceStats($courseId, $period);

        // Récupérer la liste des cours pour le menu déroulant
        $courses = courses::all();

        return view('attendance.student-dashboard', compact('student', 'stats', 'courses', 'period'));
    }

    /**
     * Affiche les statistiques détaillées pour une classe entière
     * 
     * @param Request $request Requête HTTP contenant la période
     * @param Classes $class La classe à analyser
     * @return \Illuminate\View\View Vue avec les statistiques de la classe
     */
    public function classDashboard(Request $request, Classes $class)
    {
        $period = $request->input('period', 'semester');
        $now = Carbon::now();
        
        // Calculer les dates de début/fin selon la période sélectionnée
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

        // Calculer les statistiques pour chaque étudiant de la classe
        $studentStats = [];
        foreach ($class->students as $student) {
            $studentStats[$student->id] = [
                'student' => $student,
                'stats' => $student->getAttendanceStats(null, $period)
            ];
        }

        // Calculer la moyenne globale de présence pour la classe
        $classAverage = $class->getClassAttendanceRate($startDate, $endDate);

        return view('attendance.class-dashboard', compact('class', 'studentStats', 'classAverage', 'period'));
    }

    /**
     * Affiche la liste des étudiants ayant dépassé le seuil d'absences ("droppés")
     * 
     * @param Request $request Requête HTTP contenant le filtre de cours
     * @return \Illuminate\View\View Vue avec la liste des étudiants droppés
     */
    public function droppedStudents(Request $request)
    {
        $courseId = $request->input('course_id');
        $droppedStudents = [];

        // Si un cours est sélectionné, vérifier les étudiants droppés pour ce cours
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
     * Exporte les statistiques de présence au format PDF ou Excel
     * 
     * @param Request $request Requête HTTP contenant le type d'export et les filtres
     * @return \Symfony\Component\HttpFoundation\Response Fichier PDF ou Excel à télécharger
     */
    public function exportStats(Request $request)
    {
        $type = $request->input('type', 'pdf');
        $classId = $request->input('class_id');
        $period = $request->input('period', 'semester');

        // Récupérer la classe et préparer les données pour l'export
        $class = Classes::findOrFail($classId);
        $stats = [];

        // Compiler les statistiques pour chaque étudiant
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

        // Générer le fichier selon le format demandé
        if ($type === 'pdf') {
            $pdf = PDF::loadView('attendance.export.pdf', compact('stats', 'class', 'period'));
            return $pdf->download('attendance_stats.pdf');
        } else {
            return Excel::download(new AttendanceStatsExport($stats), 'attendance_stats.xlsx');
        }
    }

    /**
     * Envoie des notifications aux coordinateurs concernant un étudiant droppé
     * 
     * @param Students $student L'étudiant droppé
     * @param Course $course Le cours concerné
     * @param float $attendanceRate Le taux de présence actuel
     * @return bool True si les notifications ont été envoyées avec succès
     */
    protected function notifyCoordinators($student, $course, $attendanceRate)
    {
        try {
            // Récupérer tous les coordinateurs du système
            $coordinators = User::where('role', 'coordinators')->get();
            
            \Log::info('Found coordinators', [
                'count' => $coordinators->count(),
                'coordinators' => $coordinators->pluck('id')
            ]);

            // Envoyer une notification à chaque coordinateur
            foreach ($coordinators as $coordinator) {
                try {
                    // Préparer les données de la notification
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

                    // Envoyer la notification
                    $coordinator->notify(new StudentDroppedNotification($notificationData));

                    \Log::info('Successfully notified coordinator', [
                        'coordinator_id' => $coordinator->id
                    ]);
                } catch (\Exception $e) {
                    // Logger l'erreur si l'envoi échoue pour un coordinateur
                    \Log::error('Failed to notify individual coordinator', [
                        'coordinator_id' => $coordinator->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            // Logger l'erreur si le processus global échoue
            \Log::error('Error in notifyCoordinators', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
} 