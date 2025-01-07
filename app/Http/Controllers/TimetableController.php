<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Classes;
use App\Models\Courses; 
use App\Models\Teachers;
use App\Models\User;
use App\Models\Days;
use App\Models\TimeSlots;

/**
 * Contrôleur pour la gestion des emplois du temps
 * 
 * Ce contrôleur permet de :
 * - Lister tous les emplois du temps
 * - Créer un nouvel emploi du temps
 * - Modifier un emploi du temps existant
 * - Supprimer un emploi du temps
 */
class TimetableController extends Controller
{
    /**
     * Affiche la liste de tous les emplois du temps
     * 
     * Charge également les données associées :
     * - Classes
     * - Enseignants et leurs utilisateurs
     * - Jours
     * - Créneaux horaires
     * - Cours
     * 
     * @return \Illuminate\View\View Vue avec toutes les données nécessaires
     */
    public function index()
    {
        $classes = \App\Models\classes::all();
        $teachers = \App\Models\Teachers::all();
        $timetables = \App\Models\Timetable::with([
            'class',
            'teacher.user',  // Chargement eager de la relation user du teacher
            'day',
            'timeSlot',
            'course'
        ])->get();
        $days = \App\Models\Days::all();
        $time_slots = \App\Models\TimeSlots::all();
        $courses = \App\Models\courses::all();

        return view('CoordinatorsTimetable', compact(
            'classes',
            'teachers', 
            'timetables',
            'days',
            'time_slots',
            'courses'
        ));
    }

    /**
     * Crée un nouvel emploi du temps
     * 
     * Valide et enregistre les données :
     * - Classe
     * - Cours
     * - Enseignant
     * - Jour
     * - Créneau horaire
     * - Couleur
     * 
     * @param Request $request Données du formulaire
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec statut
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required',
            'course_id' => 'required', 
            'teacher_id' => 'required',
            'day_id' => 'required',
            'time_slot_id' => 'required',
            'color' => 'required'
        ]);

        Timetable::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Emploi du temps créé avec succès!'
        ]);
    }

    /**
     * Met à jour un emploi du temps existant
     * 
     * Valide et met à jour les mêmes champs que pour la création
     * 
     * @param Request $request Données du formulaire
     * @param int $id Identifiant de l'emploi du temps
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec statut
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'class_id' => 'required',
            'course_id' => 'required',
            'teacher_id' => 'required', 
            'day_id' => 'required',
            'time_slot_id' => 'required',
            'color' => 'required'
        ]);

        $timetable = Timetable::findOrFail($id);
        
        $timetable->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Emploi du temps mis à jour avec succès'
        ]);
    }

    /**
     * Supprime un emploi du temps
     * 
     * @param int $id Identifiant de l'emploi du temps à supprimer
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec message de confirmation
     */
    public function destroy($id)
    {
        $timetable = Timetable::findOrFail($id);
        $timetable->delete();

        return response()->json(['message' => 'Emploi du temps supprimé avec succès']);
    }

    /**
     * Récupère les données d'un emploi du temps pour édition
     * 
     * Charge toutes les relations nécessaires :
     * - Classe
     * - Enseignant
     * - Cours
     * - Jour
     * - Créneau horaire
     * 
     * @param int $id Identifiant de l'emploi du temps
     * @return \Illuminate\Http\JsonResponse Données de l'emploi du temps
     */
    public function edit($id)
    {
        $timetable = Timetable::with(['class', 'teacher', 'course', 'day', 'timeSlot'])->findOrFail($id);
        return response()->json($timetable);
    }
}
