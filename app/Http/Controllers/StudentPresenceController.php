<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StudentPresence;
use Illuminate\Http\Request;

class StudentPresenceController extends Controller
{
    public function index()
    {
        return StudentPresence::all();
    }

    public function show($id)
    {
        return StudentPresence::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $studentPresence = StudentPresence::findOrFail($id);
        $studentPresence->update($request->all());

        return response()->json($studentPresence, 200);
    }

    public function destroy($id)
    {
        StudentPresence::destroy($id);

        return response()->json(null, 204);
    }
}