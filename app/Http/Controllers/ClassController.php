<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

/**
 * Contrôleur pour la gestion des classes
 * Permet aux coordinateurs de gérer les classes (création, modification, suppression)
 */
class ClassController extends Controller
{
    /**
     * Affiche la liste paginée des classes avec le nombre d'étudiants
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $classes = Classes::withCount('students')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('coordinator.classes.index', compact('classes'));
    }

    /**
     * Crée une nouvelle classe
     * 
     * @param Request $request Données du formulaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation du nom de la classe (requis, unique)
        $request->validate([
            'name' => 'required|string|max:255|unique:classes'
        ]);

        // Création de la classe
        Classes::create([
            'name' => $request->name
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Classe créée avec succès');
    }

    /**
     * Met à jour une classe existante
     * 
     * @param Request $request Données du formulaire
     * @param Classes $class Instance de la classe à modifier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Classes $class)
    {
        // Validation du nouveau nom (unique sauf pour la classe courante)
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id
        ]);

        // Mise à jour du nom
        $class->update([
            'name' => $request->name
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Classe modifiée avec succès');
    }

    /**
     * Supprime une classe si elle ne contient pas d'étudiants
     * 
     * @param Classes $class Instance de la classe à supprimer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Classes $class)
    {
        // Vérification qu'aucun étudiant n'est associé
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Impossible de supprimer une classe qui contient des étudiants');
        }

        // Suppression de la classe
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Classe supprimée avec succès');
    }
}
