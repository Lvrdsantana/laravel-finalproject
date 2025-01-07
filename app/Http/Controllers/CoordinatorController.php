<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Models\Days;
use App\Models\TimeSlots;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\AbsenceJustifiedNotification;
use App\Models\Justification;

class CoordinatorController extends Controller
{
    private function checkCoordinatorRole()
    {
        if (Auth::user()->role !== 'coordinators') {
            return redirect('/dashboard')->with('error', 'Accès non autorisé');
        }
        return null;
    }

    public function showTimetable()
    {
        $timetables = Timetable::with(['class', 'course', 'teacher.user'])
            ->get();
        $days = Days::all();
        $time_slots = TimeSlots::all();
        $specialCourses = ['Workshop', 'E-Learning'];

        return view('CoodinatorsTimetable', compact('timetables', 'days', 'time_slots', 'specialCourses'));
    }

    public function showAttendance(Timetable $timetable)
    {
        $check = $this->checkCoordinatorRole();
        if ($check) return $check;

        // Vérifier si c'est un cours spécial
        if (!in_array($timetable->course->name, ['Workshop', 'E-Learning'])) {
            return redirect()->back()->with('error', 'Ce cours ne nécessite pas de saisie de présence par le coordinateur');
        }

        $students = $timetable->class->students;
        $attendances = Attendance::where('timetable_id', $timetable->id)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('coordinator.attendance', compact('timetable', 'students', 'attendances'));
    }

    public function storeAttendance(Request $request, Timetable $timetable)
    {
        $check = $this->checkCoordinatorRole();
        if ($check) return $check;

        // Vérifier si c'est un cours spécial
        if (!in_array($timetable->course->name, ['Workshop', 'E-Learning'])) {
            return redirect()->back()->with('error', 'Ce cours ne nécessite pas de saisie de présence par le coordinateur');
        }

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late'
        ]);

        try {
            \DB::beginTransaction();

            // Récupérer l'ID du professeur associé à l'utilisateur coordinateur
            $teacher = Teachers::where('user_id', Auth::id())->first();
            
            // Si le coordinateur n'a pas d'entrée dans la table teachers, utiliser le professeur du cours
            if (!$teacher) {
                $teacher = $timetable->teacher;
                
                if (!$teacher) {
                    throw new \Exception('Aucun professeur trouvé pour marquer les présences');
                }
            }

            foreach ($request->attendance as $studentId => $status) {
                // Vérifier si l'étudiant existe et appartient à la classe
                $student = Students::where('id', $studentId)
                                ->where('class_id', $timetable->class_id)
                                ->first();
                                
                if (!$student) {
                    continue;
                }

                try {
                    Attendance::updateOrCreate(
                        [
                            'timetable_id' => $timetable->id,
                            'student_id' => $studentId,
                        ],
                        [
                            'status' => $status,
                            'marked_by' => $teacher->id,
                            'marked_at' => now(),
                        ]
                    );
                } catch (\Exception $e) {
                    \Log::error("Erreur pour l'étudiant $studentId: " . $e->getMessage());
                    throw $e;
                }
            }

            \DB::commit();
            return redirect()->back()->with('success', 'Présences enregistrées avec succès');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de l\'enregistrement des présences: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement des présences. Veuillez vérifier les données et réessayer.');
        }
    }

    public function edit($id)
    {
        try {
            $timetable = Timetable::with(['class', 'course', 'teacher', 'day', 'timeSlot'])
                ->findOrFail($id);

            return response()->json([
                'id' => $timetable->id,
                'class_id' => $timetable->class_id,
                'course_id' => $timetable->course_id,
                'teacher_id' => $timetable->teacher_id,
                'day_id' => $timetable->day_id,
                'time_slot_id' => $timetable->time_slot_id,
                'color' => $timetable->color ?? '#000000',
                // Relations complètes
                'class' => $timetable->class,
                'course' => $timetable->course,
                'teacher' => $timetable->teacher,
                'day' => $timetable->day,
                'time_slot' => $timetable->timeSlot
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des données',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function debugAttendanceData($timetable, $student, $teacher, $status)
    {
        \Log::info('Données de présence:', [
            'timetable_id' => $timetable->id,
            'student_id' => $student->id,
            'class_id' => $timetable->class_id,
            'student_class_id' => $student->class_id,
            'teacher_id' => $teacher->id,
            'status' => $status,
            'marked_at' => now(),
        ]);
    }

    public function showJustifyAbsence(Attendance $attendance)
    {
        // Charger toutes les relations nécessaires
        $attendance->load(['student.user', 'timetable.course', 'teacher.user']);
        
        // Vérifier que l'absence n'est pas "present"
        if ($attendance->status === 'present') {
            return redirect()->back()->with('error', 'Impossible de justifier une présence');
        }

        return view('coordinator.justify-absence', compact('attendance'));
    }

    public function justifyAbsence(Request $request, Attendance $attendance)
    {
        $request->validate([
            'justification_reason' => 'required|string|min:10'
        ]);

        try {
            DB::beginTransaction();

            // Créer ou mettre à jour la justification
            $justification = Justification::updateOrCreate(
                ['attendance_id' => $attendance->id],
                [
                    'reason' => $request->justification_reason,
                    'justified_by' => auth()->id(),
                    'justified_at' => now()
                ]
            );

            // Log pour déboguer
            Log::info('Justification créée/mise à jour', [
                'attendance_id' => $attendance->id,
                'justification_id' => $justification->id,
                'justified_by' => auth()->id()
            ]);

            // Créer les notifications
            $notificationData = [
                'type' => 'absence_justified',
                'student_name' => $attendance->student->user->name,
                'course_name' => $attendance->timetable->course->name,
                'date' => $attendance->marked_at->format('d/m/Y'),
                'reason' => $request->justification_reason,
                'justified_by' => auth()->user()->name
            ];

            // Notifier l'étudiant
            if ($attendance->student && $attendance->student->user) {
                $attendance->student->user->notify(new AbsenceJustifiedNotification($notificationData));
            }

            // Notifier l'enseignant
            if ($attendance->timetable && $attendance->timetable->teacher && $attendance->timetable->teacher->user) {
                $attendance->timetable->teacher->user->notify(new AbsenceJustifiedNotification($notificationData));
            }

            DB::commit();
            return redirect()->route('coordinator.attendance.index')
                ->with('success', 'Justification enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la justification:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function attendanceIndex()
    {
        // Récupérer toutes les absences non justifiées
        $attendances = Attendance::with([
            'student.user', 
            'timetable' => function($query) {
                $query->withTrashed();
            },
            'timetable.course',
            'timetable.class',
            'teacher.user',
            'justification.justifier'
        ])
        ->where('status', '!=', 'present')
        ->whereDoesntHave('justification')
        ->orderBy('marked_at', 'desc')
        ->paginate(15);

        // Compter les absences en attente de justification
        $pendingJustifications = Attendance::where('status', '!=', 'present')
            ->whereDoesntHave('justification')
            ->count();

        return view('coordinator.attendance.index', compact('attendances', 'pendingJustifications'));
    }
}