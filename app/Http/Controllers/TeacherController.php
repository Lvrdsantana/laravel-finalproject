<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\teachers;
use App\Models\User;

/**
 * Contrôleur pour la gestion des enseignants
 * 
 * Ce contrôleur gère toutes les opérations CRUD liées aux enseignants :
 * - Liste des enseignants
 * - Création d'un enseignant 
 * - Affichage des détails d'un enseignant
 * - Modification d'un enseignant
 * - Suppression d'un enseignant
 */
class TeacherController extends Controller
{
    /**
     * Affiche la liste de tous les enseignants
     * 
     * @return \Illuminate\View\View Vue avec la liste des enseignants
     */
    public function index()
    {
        $teachers = teachers::with('user')->get();
        return view('teachers.index', compact('teachers'));
    }

    /**
     * Affiche le formulaire de création d'un enseignant
     * 
     * @return \Illuminate\View\View Vue du formulaire de création
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Enregistre un nouvel enseignant dans la base de données
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des enseignants
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        teachers::create([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    /**
     * Affiche les détails d'un enseignant spécifique
     * 
     * @param int $id Identifiant de l'enseignant
     * @return \Illuminate\View\View Vue avec les détails de l'enseignant
     */
    public function show($id)
    {
        $teacher = teachers::with('user')->findOrFail($id);
        return view('teachers.show', compact('teacher'));
    }

    /**
     * Affiche le formulaire d'édition d'un enseignant
     * 
     * @param int $id Identifiant de l'enseignant
     * @return \Illuminate\View\View Vue du formulaire d'édition
     */
    public function edit($id)
    {
        $teacher = teachers::findOrFail($id);
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Met à jour les informations d'un enseignant
     * 
     * @param Request $request Les données du formulaire
     * @param int $id Identifiant de l'enseignant
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des enseignants
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $teacher = teachers::findOrFail($id);
        $teacher->update([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    /**
     * Supprime un enseignant de la base de données
     * 
     * @param int $id Identifiant de l'enseignant
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste des enseignants
     */
    public function destroy($id)
    {
        $teacher = teachers::findOrFail($id);
        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}
