<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\User;

class ParentController extends Controller
{
    public function index()
    {
        // Récupère tous les parents avec leur utilisateur associé
        $parents = ParentModel::with('user')->get();

        return view('parents.index', compact('parents'));
    }

    public function create()
    {
        return view('parents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // Vérifie que l'utilisateur existe
        ]);

        ParentModel::create([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('parents.index')->with('success', 'Parent created successfully.');
    }

    public function show($id)
    {
        // Récupère un parent spécifique avec l'utilisateur associé
        $parent = ParentModel::with('user')->findOrFail($id);

        return view('parents.show', compact('parent'));
    }

    public function edit($id)
    {
        // Récupère un parent spécifique pour l'édition
        $parent = ParentModel::findOrFail($id);

        return view('parents.edit', compact('parent'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $parent = ParentModel::findOrFail($id);
        $parent->update([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('parents.index')->with('success', 'Parent updated successfully.');
    }

    public function destroy($id)
    {
        $parent = ParentModel::findOrFail($id);
        $parent->delete();

        return redirect()->route('parents.index')->with('success', 'Parent deleted successfully.');
    }
}
