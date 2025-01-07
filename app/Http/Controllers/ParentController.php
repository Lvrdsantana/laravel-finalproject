<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\User;

/**
 * Contrôleur pour la gestion des parents d'élèves
 * 
 * Ce contrôleur gère les opérations CRUD (Create, Read, Update, Delete) sur les parents :
 * - Affichage de la liste des parents
 * - Création d'un nouveau parent
 * - Modification des informations d'un parent
 * - Suppression d'un parent
 */
class ParentController extends Controller
{
    /**
     * Affiche la liste de tous les parents
     * 
     * @return \Illuminate\View\View Vue avec la liste des parents
     */
    public function index()
    {
        // Récupère tous les parents avec leur utilisateur associé via la relation
        $parents = ParentModel::with('user')->get();

        return view('parents.index', compact('parents'));
    }

    /**
     * Affiche le formulaire de création d'un parent
     * 
     * @return \Illuminate\View\View Vue du formulaire de création
     */
    public function create()
    {
        return view('parents.create');
    }

    /**
     * Enregistre un nouveau parent dans la base de données
     * 
     * @param Request $request Les données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function store(Request $request)
    {
        // Validation des données avec vérification que l'utilisateur existe
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Création du parent avec l'ID utilisateur validé
        ParentModel::create([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('parents.index')->with('success', 'Parent created successfully.');
    }

    /**
     * Affiche les détails d'un parent spécifique
     * 
     * @param int $id L'identifiant du parent
     * @return \Illuminate\View\View Vue avec les détails du parent
     */
    public function show($id)
    {
        // Récupère le parent avec ses relations utilisateur
        $parent = ParentModel::with('user')->findOrFail($id);

        return view('parents.show', compact('parent'));
    }

    /**
     * Affiche le formulaire d'édition d'un parent
     * 
     * @param int $id L'identifiant du parent à modifier
     * @return \Illuminate\View\View Vue du formulaire d'édition
     */
    public function edit($id)
    {
        // Récupère le parent à modifier
        $parent = ParentModel::findOrFail($id);

        return view('parents.edit', compact('parent'));
    }

    /**
     * Met à jour les informations d'un parent
     * 
     * @param Request $request Les nouvelles données
     * @param int $id L'identifiant du parent
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function update(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Mise à jour du parent avec les données validées
        $parent = ParentModel::findOrFail($id);
        $parent->update([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('parents.index')->with('success', 'Parent updated successfully.');
    }

    /**
     * Supprime un parent de la base de données
     * 
     * @param int $id L'identifiant du parent à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès
     */
    public function destroy($id)
    {
        $parent = ParentModel::findOrFail($id);
        $parent->delete();

        return redirect()->route('parents.index')->with('success', 'Parent deleted successfully.');
    }
}
