<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Material;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    private function authorizeTeacher(Classroom $classroom)
    {
        $pivot = $classroom->users()->where('user_id', Auth::id())->first();
        if (!$pivot || $pivot->pivot->role !== 'teacher') {
            abort(403, 'Only teachers can perform this action.');
        }
    }

    public function store(Request $request, Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $classroom->materials()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Material uploaded successfully!');
    }

    public function update(Request $request, Material $material)
    {
        $this->authorizeTeacher($material->classroom);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $material->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Material updated successfully!');
    }

    public function destroy(Material $material)
    {
        $this->authorizeTeacher($material->classroom);

        $material->delete();

        return back()->with('success', 'Material deleted successfully!');
    }
}
