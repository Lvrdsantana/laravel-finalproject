<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable; // Modèle pour gérer les emplois du temps
use App\Models\Attendance; // Modèle pour gérer les présences
use Illuminate\Support\Facades\Auth; // Gestion de l'authentification
use App\Models\Days; // Modèle pour les jours de la semaine
use App\Models\TimeSlots; // Modèle pour les créneaux horaires
use App\Models\Students; // Modèle pour les étudiants
use App\Models\Teachers; // Modèle pour les enseignants
use Illuminate\Support\Facades\DB; // Gestion de la base de données
use Illuminate\Support\Facades\Log; // Gestion des logs
use App\Notifications\AbsenceJustifiedNotification; // Notification pour les absences justifiées
use App\Models\Justification; // Modèle pour les justifications d'absence

/**
 * Contrôleur gérant les fonctionnalités des coordinateurs
 * 
 * Ce contrôleur gère :
 * - L'affichage et la gestion des emplois du temps
 * - La gestion des présences pour les cours spéciaux (Workshop, E-Learning)
 * - La justification des absences
 * - Le suivi des absences
 */
class CoordinatorController extends Controller
{
    /**
     * Vérifie que l'utilisateur connecté a bien le rôle de coordinateur
     * 
     * @return \Illuminate\Http\RedirectResponse|null Redirige si pas coordinateur, null sinon
     */
    private function checkCoordinatorRole()
    {
        if (Auth::user()->role !== 'coordinators') {
            return redirect('/dashboard')->with('error', 'Accès non autorisé');
        }
        return null;
    }

    /**
     * Affiche l'emploi du temps avec toutes les informations nécessaires
     * 
     * @return \Illuminate\View\View Vue de l'emploi du temps
     */
    public function showTimetable()
    {
        $timetables = Timetable::with(['class', 'course', 'teacher.user'])
            ->get();
        $days = Days::all();
        $time_slots = TimeSlots::all();
        $specialCourses = ['Workshop', 'E-Learning'];

        return view('CoodinatorsTimetable', compact('timetables', 'days', 'time_slots', 'specialCourses'));
    }

    /**
     * Affiche la page de gestion des présences pour un créneau spécifique
     * 
     * @param Timetable $timetable Le créneau concerné
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showAttendance(Timetable $timetable)
    {
        $check = $this->checkCoordinatorRole();
        if ($check) return $check;

        // Vérifier si c'est un cours spécial (seuls les coordinateurs peuvent gérer ces présences)
        if (!in_array($timetable->course->name, ['Workshop', 'E-Learning'])) {
            return redirect()->back()->with('error', 'Ce cours ne nécessite pas de saisie de présence par le coordinateur');
        }

        $students = $timetable->class->students;
        $attendances = Attendance::where('timetable_id', $timetable->id)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('coordinator.attendance', compact('timetable', 'students', 'attendances'));
    }

    /**
     * Enregistre les présences pour un créneau donné
     * 
     * @param Request $request La requête contenant les données de présence
     * @param Timetable $timetable Le créneau concerné
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAttendance(Request $request, Timetable $timetable)
    {
        $check = $this->checkCoordinatorRole();
        if ($check) return $check;

        // Vérification du type de cours
        if (!in_array($timetable->course->name, ['Workshop', 'E-Learning'])) {
            return redirect()->back()->with('error', 'Ce cours ne nécessite pas de saisie de présence par le coordinateur');
        }

        // Validation des données reçues
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late'
        ]);

        try {
            \DB::beginTransaction();

            // Récupération du professeur responsable
            $teacher = Teachers::where('user_id', Auth::id())->first();
            
            // Fallback sur le professeur du cours si nécessaire
            if (!$teacher) {
                $teacher = $timetable->teacher;
                
                if (!$teacher) {
                    throw new \Exception('Aucun professeur trouvé pour marquer les présences');
                }
            }

            // Traitement de chaque étudiant
            foreach ($request->attendance as $studentId => $status) {
                // Vérification de l'appartenance de l'étudiant à la classe
                $student = Students::where('id', $studentId)
                                ->where('class_id', $timetable->class_id)
                                ->first();
                                
                if (!$student) {
                    continue;
                }

                try {
                    // Création ou mise à jour de la présence
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

    /**
     * Récupère les informations d'un créneau pour l'édition
     * 
     * @param int $id L'ID du créneau
     * @return \Illuminate\Http\JsonResponse
     */
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
                // Relations complètes pour l'interface
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

    /**
     * Fonction de debug pour logger les données de présence
     */
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

    /**
     * Affiche le formulaire de justification d'absence
     * 
     * @param Attendance $attendance L'absence à justifier
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showJustifyAbsence(Attendance $attendance)
    {
        // Chargement des relations pour la vue
        $attendance->load(['student.user', 'timetable.course', 'teacher.user']);
        
        // Vérification du statut
        if ($attendance->status === 'present') {
            return redirect()->back()->with('error', 'Impossible de justifier une présence');
        }

        return view('coordinator.justify-absence', compact('attendance'));
    }

    /**
     * Traite la justification d'une absence
     * 
     * @param Request $request La requête avec les données de justification
     * @param Attendance $attendance L'absence concernée
     * @return \Illuminate\Http\RedirectResponse
     */
    public function justifyAbsence(Request $request, Attendance $attendance)
    {
        $request->validate([
            'justification_reason' => 'required|string|min:10'
        ]);

        try {
            DB::beginTransaction();

            // Création/mise à jour de la justification
            $justification = Justification::updateOrCreate(
                ['attendance_id' => $attendance->id],
                [
                    'reason' => $request->justification_reason,
                    'justified_by' => auth()->id(),
                    'justified_at' => now()
                ]
            );

            // Log de debug
            Log::info('Justification créée/mise à jour', [
                'attendance_id' => $attendance->id,
                'justification_id' => $justification->id,
                'justified_by' => auth()->id()
            ]);

            // Préparation des données pour les notifications
            $notificationData = [
                'type' => 'absence_justified',
                'student_name' => $attendance->student->user->name,
                'course_name' => $attendance->timetable->course->name,
                'date' => $attendance->marked_at->format('d/m/Y'),
                'reason' => $request->justification_reason,
                'justified_by' => auth()->user()->name
            ];

            // Envoi des notifications aux concernés
            if ($attendance->student && $attendance->student->user) {
                $attendance->student->user->notify(new AbsenceJustifiedNotification($notificationData));
            }

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

    /**
     * Affiche la liste des absences à traiter
     * 
     * @return \Illuminate\View\View
     */
    public function attendanceIndex()
    {
        // Récupération des absences non justifiées avec leurs relations
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

        // Compteur pour le tableau de bord
        $pendingJustifications = Attendance::where('status', '!=', 'present')
            ->whereDoesntHave('justification')
            ->count();

        return view('coordinator.attendance.index', compact('attendances', 'pendingJustifications'));
    }
}