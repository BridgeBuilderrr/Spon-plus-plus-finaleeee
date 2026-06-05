<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Notifications\ClassroomActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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

        $assignment = $classroom->assignments()->create([
            'title' => $request->title,
            'description' => $request->description,
            'open_date' => $request->open_date,
            'due_date' => $request->due_date,
            'files' => $request->input('files', [])
        ]);

        Notification::send($classroom->users, new ClassroomActivityNotification($classroom, $assignment, 'assignment'));

        return redirect()->route('courses.show', $classroom)->with('success', 'Assignment posted!');
    }

    public function update(Request $request, Classroom $classroom, Assignment $assignment)
    {
        if ($classroom->teacher_id !== auth()->id()) return abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
        ]);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'open_date' => $request->open_date,
            'files' => $request->input('files', $assignment->files)
        ]);

        return back()->with('success', 'Assignment updated!');
    }

    public function destroy(Classroom $classroom, Assignment $assignment)
    {
        if ($classroom->teacher_id !== auth()->id()) return abort(403);
        $assignment->delete();
        return back()->with('success', 'Assignment deleted.');
    }
}
