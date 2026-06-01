<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Classroom;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'open_date' => 'nullable|date|after_or_equal:now',
            'due_date' => 'required|date|after:now|after_or_equal:open_date',
        ]);

        $classroom->assignments()->create([
            'title' => $request->title,
            'description' => $request->description,
            'open_date' => $request->open_date,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('courses.show', $classroom)->with('success', 'Assignment posted!');
    }
}
