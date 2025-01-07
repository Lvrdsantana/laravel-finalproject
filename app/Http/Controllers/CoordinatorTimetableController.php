<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\TimetableHistory;
use App\Models\Courses;
use App\Models\Classes;
use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;

/**
 * Contrôleur pour la gestion des emplois du temps par les coordinateurs
 * 
 * Ce contrôleur gère :
 * - L'affichage et la modification des emplois du temps
 * - L'historique des modifications
 * - L'export des données en PDF et Excel
 */
class CoordinatorTimetableController extends Controller
{
    /**
     * Affiche la page principale des emplois du temps
     * Inclut la liste des cours, classes, professeurs et l'historique récent
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $courses = Courses::all();
        $classes = Classes::all();
        $teachers = Teachers::all();
        $timetables = Timetable::with(['course', 'class', 'teacher'])
            ->get()
            ->groupBy('class_id');
        
        // Récupérer l'historique global pour la vue
        $timetableHistories = TimetableHistory::with(['modifier', 'class', 'course', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->take(50) // Limiter aux 50 dernières modifications
            ->get();

        return view('backup.CoodinatorsTimetable', compact(
            'courses',
            'classes',
            'teachers',
            'timetables',
            'timetableHistories'
        ));
    }

    /**
     * Crée un nouveau créneau dans l'emploi du temps
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $timetable = Timetable::create($validated);

        return redirect()->back()->with('success', 'Emploi du temps créé avec succès');
    }

    /**
     * Affiche l'historique des modifications pour un emploi du temps spécifique
     * 
     * @param Timetable $timetable L'emploi du temps concerné
     * @return \Illuminate\View\View
     */
    public function history(Timetable $timetable)
    {
        $histories = $timetable->histories()
            ->with(['modifier', 'class', 'course', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('coordinator.timetable.history', compact('timetable', 'histories'));
    }

    /**
     * Filtre l'historique selon différents critères (AJAX)
     * 
     * @param Request $request Les critères de filtrage
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterHistory(Request $request)
    {
        $query = TimetableHistory::with(['modifier', 'class', 'course', 'teacher'])
            ->orderBy('created_at', 'desc');

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $histories = $query->get()->map(function ($history) {
            return [
                'created_at' => $history->created_at->format('Y-m-d H:i:s'),
                'class_name' => $history->class->name,
                'course_name' => $history->course->name,
                'teacher_name' => $history->teacher->name,
                'action' => ucfirst($history->action),
                'modifier_name' => $history->modifier->name,
                'changes' => $history->changes
            ];
        });

        return response()->json($histories);
    }

    /**
     * Met à jour un créneau d'emploi du temps existant
     * 
     * @param Request $request Les nouvelles données
     * @param Timetable $timetable Le créneau à modifier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $timetable->update($validated);

        return redirect()->back()->with('success', 'Emploi du temps mis à jour avec succès');
    }

    /**
     * Supprime un créneau d'emploi du temps
     * 
     * @param Timetable $timetable Le créneau à supprimer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return redirect()->back()->with('success', 'Emploi du temps supprimé avec succès');
    }

    /**
     * Affiche la page principale de l'historique avec des filtres
     * 
     * @param Request $request Les critères de filtrage
     * @return \Illuminate\View\View
     */
    public function historyIndex(Request $request)
    {
        $query = TimetableHistory::with(['modifier', 'class', 'course', 'teacher', 'timetable']);

        // Filtre par classe
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filtre par type d'action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filtre par période
        if ($request->period) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Recherche textuelle
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('class', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('course', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('teacher', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('modifier', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Statistiques
        $todayCount = TimetableHistory::whereDate('created_at', today())->count();
        $createdCount = TimetableHistory::where('action', 'created')->count();
        $updatedCount = TimetableHistory::where('action', 'updated')->count();
        $deletedCount = TimetableHistory::where('action', 'deleted')->count();

        $histories = $query->orderBy('created_at', 'desc')->paginate(20);
        $classes = Classes::all();

        return view('coordinator.timetable.history-index', compact(
            'histories',
            'classes',
            'todayCount',
            'createdCount',
            'updatedCount',
            'deletedCount'
        ));
    }

    /**
     * Exporte l'historique au format PDF ou Excel
     * 
     * @param Request $request Les critères d'export
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportHistory(Request $request)
    {
        $format = $request->format ?? 'pdf';
        $query = TimetableHistory::with(['modifier', 'class', 'course', 'teacher', 'timetable']);

        // Appliquer les mêmes filtres que pour l'affichage
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $histories = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'pdf') {
            $pdf = PDF::loadView('coordinator.timetable.history-pdf', compact('histories'));
            return $pdf->download('historique-emplois-du-temps.pdf');
        } else {
            return Excel::download(new TimetableHistoryExport($histories), 'historique-emplois-du-temps.xlsx');
        }
    }
} 