<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Teachers;
use App\Models\Days;
use App\Models\TimeSlots;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\StudentDroppedNotification;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        // Récupérer les emplois du temps
        $timetables = Timetable::with(['class', 'course', 'timeSlot'])
            ->where('teacher_id', $teacher->id)
            ->get();

        // Calculer le nombre total d'étudiants uniques
        $totalStudents = $timetables->pluck('class.students')->flatten()->unique('id')->count();

        // Calculer le nombre total de cours
        $totalCourses = $timetables->pluck('course.name')->unique()->count();

        // Récupérer les cours d'aujourd'hui
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek;
        $todayClasses = $timetables->where('day_id', $dayOfWeek)
            ->sortBy('timeSlot.start_time');

        // Calculer les statistiques de présence
        $attendanceStats = [];
        foreach ($timetables as $timetable) {
            $totalAttendances = Attendance::where('timetable_id', $timetable->id)->count();
            $presentAttendances = Attendance::where('timetable_id', $timetable->id)
                ->where('status', 'present')
                ->count();
            
            if ($totalAttendances > 0) {
                $attendanceRate = ($presentAttendances / $totalAttendances) * 100;
                $attendanceStats[$timetable->course->name] = round($attendanceRate, 1);
            }
        }

        // Calculer la moyenne globale de présence
        $averageAttendance = count($attendanceStats) > 0 ? array_sum($attendanceStats) / count($attendanceStats) : 0;

        // Récupérer les prochains cours (pour la semaine à venir)
        $upcomingLessons = collect();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();

        for ($date = $startOfWeek; $date <= $endOfWeek; $date = $date->copy()->addDay()) {
            $dayLessons = $timetables->where('day_id', $date->dayOfWeek)
                ->map(function ($timetable) use ($date) {
                    $timetable->date = $date;
                    return $timetable;
                });
            $upcomingLessons = $upcomingLessons->concat($dayLessons);
        }

        $upcomingLessons = $upcomingLessons->filter(function ($lesson) use ($today) {
            return $lesson->date->isAfter($today);
        })->sortBy('date')->take(5);

        return view('TeacherDashboard', compact(
            'timetables',
            'totalStudents',
            'totalCourses',
            'todayClasses',
            'attendanceStats',
            'averageAttendance',
            'upcomingLessons'
        ));
    }

    public function timetable()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $timetables = Timetable::with(['class', 'course', 'timeSlot'])
            ->where('teacher_id', $teacher->id)
            ->get();

        $days = Days::all();
        $time_slots = TimeSlots::all();

        return view('teacher.timetable', compact('timetables', 'days', 'time_slots'));
    }

    public function profile()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        $timetables = Timetable::with(['course'])
            ->where('teacher_id', $teacher->id)
            ->get();

        return view('teacher.profile', compact('teacher', 'timetables'));
    }

    public function showAttendance(Timetable $timetable)
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher || $teacher->id !== $timetable->teacher_id) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        // Vérifier si c'est un cours spécial
        if (in_array($timetable->course->name, ['Workshop', 'E-Learning'])) {
            return redirect()->back()->with('error', 'La saisie des présences pour ce type de cours est réservée aux coordinateurs');
        }

        // Vérifier si la séance est dans la période de 2 semaines
        if (now()->subWeeks(2)->greaterThan(\Carbon\Carbon::parse($timetable->date))) {
            return redirect()->back()->with('error', 'La période de saisie est expirée');
        }

        $students = $timetable->class->students;
        $attendances = Attendance::where('timetable_id', $timetable->id)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('teacher.attendance', compact('timetable', 'students', 'attendances'));
    }

    public function storeAttendance(Request $request, Timetable $timetable)
    {
        try {
            DB::beginTransaction();
            
            $teacher = Auth::user()->teacher;
            if (!$teacher) {
                throw new \Exception('Enseignant non trouvé');
            }

            foreach ($request->attendance as $studentId => $status) {
                // 1. Enregistrer la présence
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'timetable_id' => $timetable->id
                    ],
                    [
                        'status' => $status,
                        'marked_by' => $teacher->id,
                        'marked_at' => now()
                    ]
                );

                // 2. Récupérer les données nécessaires
                $student = \App\Models\Students::find($studentId);
                $course = $timetable->course;
                
                // 3. Calculer les statistiques avec logging détaillé
                \Log::info('Début du calcul des statistiques', [
                    'student_id' => $studentId,
                    'course_id' => $course->id
                ]);

                $attendanceQuery = Attendance::where('student_id', $studentId)
                    ->whereHas('timetable', function($query) use ($course) {
                        $query->where('course_id', $course->id);
                    });

                $totalSessions = $attendanceQuery->count();

                $presentSessions = $attendanceQuery->where('status', 'present')->count();

                \Log::info('Statistiques calculées', [
                    'total_sessions' => $totalSessions,
                    'present_sessions' => $presentSessions
                ]);

                $attendanceRate = $totalSessions > 0 ? ($presentSessions / $totalSessions) * 100 : 0;

                // 4. Mettre à jour la note d'assiduité
                try {
                    $attendanceGrade = \App\Models\AttendanceGrade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'course_id' => $course->id,
                            'semester' => 'S1'
                        ],
                        [
                            'grade' => ($attendanceRate / 100) * 20,
                            'total_sessions' => $totalSessions,
                            'attended_sessions' => $presentSessions,
                            'academic_year' => '2023-2024'
                        ]
                    );

                    \Log::info('Note d\'assiduité mise à jour', [
                        'grade_id' => $attendanceGrade->id,
                        'grade' => $attendanceGrade->grade
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de la mise à jour de la note d\'assiduité', [
                        'error' => $e->getMessage()
                    ]);
                }

                // 5. Gérer les notifications si taux < 30%
                if ($attendanceRate <= 30) {
                    $notificationData = [
                        'type' => 'student_dropped',
                        'student_name' => $student->user->name,
                        'course_name' => $course->name,
                        'attendance_rate' => round($attendanceRate, 1),
                        'message' => "L'étudiant {$student->user->name} a été droppé du cours {$course->name} (Taux de présence: " . round($attendanceRate, 1) . "%)"
                    ];

                    // Notifier les coordinateurs
                    $coordinators = \App\Models\User::where('role', 'coordinators')->get();
                    foreach ($coordinators as $coordinator) {
                        $this->createNotification($coordinator, $notificationData);
                    }

                    // Notifier l'enseignant
                    if ($course->teacher && $course->teacher->user) {
                        $this->createNotification($course->teacher->user, $notificationData);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Présences enregistrées avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'enregistrement des présences:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement des présences');
        }
    }

    // Nouvelle méthode pour créer les notifications
    protected function createNotification($user, $notificationData)
    {
        try {
            // Vérifier si une notification similaire existe déjà pour cet utilisateur
            $existingNotification = DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('data->student_name', $notificationData['student_name'])
                ->where('data->course_name', $notificationData['course_name'])
                ->where('created_at', '>', now()->subHours(24))
                ->exists();

            if (!$existingNotification) {
                // Utiliser directement la classe StudentDroppedNotification
                $notification = new \App\Notifications\StudentDroppedNotification(
                    $notificationData['student_name'],
                    $notificationData['course_name'],
                    $notificationData['attendance_rate']
                );
                
                $user->notify($notification);

                \Log::info('Notification créée avec succès', [
                    'user_id' => $user->id,
                    'notification_data' => $notificationData
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function notifications()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }

        // Utiliser paginate directement sur la requête DB
        $notifications = \DB::table('notifications')
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);  // Pagination ici avant de mapper

        // Transformer les résultats après la pagination
        $notifications->getCollection()->transform(function ($notification) {
            return (object) [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => json_decode($notification->data, true),
                'read_at' => $notification->read_at,
                'created_at' => \Carbon\Carbon::parse($notification->created_at)
            ];
        });

        \Log::info('Fetching notifications', [
            'user_id' => Auth::id(),
            'count' => $notifications->count(),
            'current_page' => $notifications->currentPage(),
            'total_pages' => $notifications->lastPage()
        ]);

        return view('teacher.notifications', compact('notifications'));
    }
} 