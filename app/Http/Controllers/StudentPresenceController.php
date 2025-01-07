<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StudentPresence;
use Illuminate\Http\Request;

/**INACHEVÉ
 * 
 * Contrôleur pour gérer les présences des étudiants via l'API
 * 
 * Ce contrôleur permet de :
 * - Lister toutes les présences
 * - Afficher une présence spécifique
 * - Mettre à jour une présence
 * - Supprimer une présence
 */
class StudentPresenceController extends Controller
{
    /**
     * Retourne la liste de toutes les présences
     * 
     * @return \Illuminate\Database\Eloquent\Collection Collection de toutes les présences
     */
    public function index()
    {
        return StudentPresence::all();
    }

    /**
     * Affiche une présence spécifique
     * 
     * @param int $id Identifiant de la présence
     * @return StudentPresence La présence demandée
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la présence n'existe pas
     */
    public function show($id)
    {
        return StudentPresence::findOrFail($id);
    }

    /**
     * Met à jour une présence existante
     * 
     * @param Request $request Données de la requête
     * @param int $id Identifiant de la présence à modifier
     * @return \Illuminate\Http\JsonResponse Présence mise à jour
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si la présence n'existe pas
     */
    public function update(Request $request, $id)
    {
        $studentPresence = StudentPresence::findOrFail($id);
        $studentPresence->update($request->all());

        return response()->json($studentPresence, 200);
    }

    /**
     * Supprime une présence
     * 
     * @param int $id Identifiant de la présence à supprimer
     * @return \Illuminate\Http\JsonResponse Réponse vide avec code 204
     */
    public function destroy($id)
    {
        StudentPresence::destroy($id);

        return response()->json(null, 204);
    }
}