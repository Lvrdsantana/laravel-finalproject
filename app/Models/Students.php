<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Notifications\StudentDroppedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\courses;
use Illuminate\Support\Facades\Log;

class Students extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'class_id'
    ];

    /**
     * Obtenir l'utilisateur associé à l'étudiant
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function attendanceGrades()
    {
        return $this->hasMany(AttendanceGrade::class);
    }

    /**
     * Calcule la note d'assiduité pour une matière donnée
     */
    public function calculateAttendanceGrade($courseId, $semester = null)
    {
        $query = Attendance::where('student_id', $this->id)
            ->whereHas('timetable', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });

        if ($semester) {
            $query->whereHas('timetable', function ($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }

        $totalSessions = $query->count();
        $presentSessions = $query->where('status', 'present')->count();

        // Calcul de la note sur 20 avec règle de trois
        $grade = $totalSessions > 0 ? (20 * $presentSessions / $totalSessions) : 0;

        // Enregistrement de la note
        AttendanceGrade::updateOrCreate(
            [
                'student_id' => $this->id,
                'course_id' => $courseId,
                'semester' => $semester
            ],
            [
                'grade' => $grade,
                'total_sessions' => $totalSessions,
                'attended_sessions' => $presentSessions,
                'academic_year' => Carbon::now()->year
            ]
        );

        return $grade;
    }

    /**
     * Calcule le taux de présence pour une période donnée
     */
    public function getAttendanceRate($startDate = null, $endDate = null, $courseId = null)
    {
        try {
            // Construire la requête de base
            $query = \DB::table('attendances')
                ->where('student_id', $this->id);

            // Log pour déboguer
            \Log::info('Building attendance query', [
                'student_id' => $this->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'course_id' => $courseId
            ]);

            // Ajouter les filtres de date
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            // Ajouter le filtre de cours
            if ($courseId) {
                $query->whereExists(function ($q) use ($courseId) {
                    $q->select(\DB::raw(1))
                        ->from('timetables')
                        ->whereRaw('timetables.id = attendances.timetable_id')
                        ->where('timetables.course_id', $courseId);
                });
            }

            // Compter le total des sessions
            $totalSessions = $query->count();

            // Si aucune session, retourner 0
            if ($totalSessions === 0) {
                \Log::info('No sessions found', ['student_id' => $this->id]);
                return 0;
            }

            // Compter les présences
            $presentSessions = (clone $query)
                ->where('status', 'present')
                ->count();

            // Calculer le taux
            $rate = ($presentSessions / $totalSessions) * 100;

            \Log::info('Attendance calculation result', [
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'rate' => $rate
            ]);

            return $rate;
        } catch (\Exception $e) {
            \Log::error('Error in getAttendanceRate', [
                'error' => $e->getMessage(),
                'student_id' => $this->id
            ]);
            return 0;
        }
    }

    /**
     * Vérifie si l'étudiant doit être droppé d'une matière
     */
    public function checkDropStatus($courseId)
    {
        try {
            $course = courses::findOrFail($courseId);
            
            // Calculer le taux de présence
            $totalSessions = Attendance::whereHas('timetable', function($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })->where('student_id', $this->id)->count();

            $presentSessions = $this->attendances()
                ->where('status', 'present')
                ->whereHas('timetable', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })->count();

            $rate = $totalSessions > 0 ? ($presentSessions / $totalSessions) * 100 : 0;

            // Si le taux est inférieur à 30%, envoyer une notification
            if ($rate <= 30) {
                // Notifier les coordinateurs
                $coordinators = User::where('role', 'coordinators')->get();
                
                foreach ($coordinators as $coordinator) {
                    $coordinator->notify(new StudentDroppedNotification(
                        $this->user->name,
                        $course->name,
                        round($rate, 1)
                    ));
                }

                // Notifier l'enseignant du cours
                if ($course->teacher && $course->teacher->user) {
                    $course->teacher->user->notify(new StudentDroppedNotification(
                        $this->user->name,
                        $course->name,
                        round($rate, 1)
                    ));
                }

                Log::info('Drop notifications sent', [
                    'student_id' => $this->id,
                    'course_id' => $courseId,
                    'rate' => $rate,
                    'coordinators' => $coordinators->pluck('id'),
                    'teacher' => $course->teacher->user->id ?? null
                ]);
            }

            return [
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'rate' => $rate
            ];

        } catch (\Exception $e) {
            Log::error('Error in checkDropStatus', [
                'error' => $e->getMessage(),
                'student_id' => $this->id,
                'course_id' => $courseId,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Récupère les statistiques complètes de présence
     */
    public function getAttendanceStats($courseId = null, $period = 'semester')
    {
        try {
            // Récupérer les présences
            $query = Attendance::where('student_id', $this->id)
                ->join('timetables', 'attendances.timetable_id', '=', 'timetables.id');
                
            if ($courseId) {
                $query->where('timetables.course_id', $courseId);
            }

            // Compter le total des sessions
            $totalSessions = $query->count();
            
            // Compter les présences
            $presentSessions = (clone $query)
                ->where('attendances.status', 'present')
                ->count();

            // Calculer le taux
            $rate = $totalSessions > 0 ? ($presentSessions / $totalSessions) * 100 : 0;

            // Calculer la note sur 20
            $grade = $totalSessions > 0 ? (20 * $presentSessions / $totalSessions) : 0;

            // Déterminer si l'étudiant est dropp
            $isDropped = $totalSessions > 1 && $rate < 30;

            \Log::info('Attendance Stats Calculation', [
                'student_id' => $this->id,
                'course_id' => $courseId,
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'rate' => $rate,
                'grade' => $grade,
                'is_dropped' => $isDropped
            ]);

            return [
                'attendance_rate' => round($rate, 1),
                'attendance_grade' => round($grade, 1),
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'period' => $period,
                'is_dropped' => $isDropped,
                'course_id' => $courseId
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getAttendanceStats', [
                'error' => $e->getMessage(),
                'student_id' => $this->id,
                'course_id' => $courseId
            ]);
            return [
                'attendance_rate' => 0,
                'attendance_grade' => 0,
                'total_sessions' => 0,
                'present_sessions' => 0,
                'period' => $period,
                'is_dropped' => false,
                'course_id' => $courseId
            ];
        }
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_id', 'class_id');
    }
} 