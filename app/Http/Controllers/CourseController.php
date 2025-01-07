<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

/**
 * Contrôleur pour la gestion des cours
 * 
 * Ce contrôleur gère les opérations CRUD (Create, Read, Update, Delete) sur les cours :
 * - Création d'un nouveau cours
 * - Affichage de la liste des cours
 * - Mise à jour d'un cours existant 
 * - Suppression d'un cours
 */
class CourseController extends Controller
{
    /**
     * Crée un nouveau cours dans la base de données
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function store(Request $request)
    {
        // Validation des données reçues
        $request->validate([
            'name' => 'required|string|max:255|unique:courses', // Nom requis et unique
            'description' => 'nullable|string' // Description optionnelle
        ]);

        // Création du cours avec les données validées
        $course = Course::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Course created successfully');
    }

    /**
     * Retourne la liste complète des cours au format JSON
     * 
     * @return \Illuminate\Http\JsonResponse Liste des cours
     */
    public function index()
    {
        $courses = Course::all();
        return response()->json($courses);
    }

    /**
     * Met à jour un cours existant
     * 
     * @param Request $request Les nouvelles données
     * @param Course $course Le cours à modifier
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function update(Request $request, Course $course)
    {
        // Validation avec règle d'unicité excluant le cours actuel
        $request->validate([
            'name' => 'required|string|max:255|unique:courses,name,' . $course->id,
            'description' => 'nullable|string'
        ]);

        // Mise à jour avec les données validées
        $course->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Course updated successfully');
    }

    /**
     * Supprime un cours de la base de données
     * 
     * @param Course $course Le cours à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->back()->with('success', 'Course deleted successfully');
    }
}
