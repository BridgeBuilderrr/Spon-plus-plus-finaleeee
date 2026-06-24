<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Notifications\ClassroomActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AssignmentController extends Controller
{
    public function create(Classroom $classroom)
    {
        if ($classroom->teacher_id !== auth()->id()) return abort(403);
        return view('courses.assignments.create', compact('classroom'));
    }

    public function edit(Classroom $classroom, Assignment $assignment)
    {
        if ($classroom->teacher_id !== auth()->id()) return abort(403);
        return view('courses.assignments.edit', compact('classroom', 'assignment'));
    }

    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignment_type' => 'required|string|in:essay,pilihan_ganda',
            'open_date' => 'nullable|date',
            'due_date' => 'required|date',
            'questions' => 'nullable|array',
        ]);

        $assignment = $classroom->assignments()->create([
            'title' => $request->title,
            'assignment_type' => $request->input('assignment_type', 'essay'),
            'description' => $request->description ?? '',
            'questions' => $request->input('questions'),
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
            'description' => 'nullable|string',
            'assignment_type' => 'required|string|in:essay,pilihan_ganda',
            'due_date' => 'required|date',
            'questions' => 'nullable|array',
        ]);

        $assignment->update([
            'title' => $request->title,
            'assignment_type' => $request->assignment_type,
            'description' => $request->description ?? '',
            'due_date' => $request->due_date,
            'open_date' => $request->open_date,
            'questions' => $request->input('assignment_type') === 'pilihan_ganda' ? $request->input('questions', []) : null,
            'files' => $request->input('assignment_type') === 'essay' ? $request->input('files', []) : null
        ]);

        return redirect()->route('courses.show', $classroom)->with('success', 'Assignment updated!');
    }

    public function destroy(Classroom $classroom, Assignment $assignment)
    {
        if ($classroom->teacher_id !== auth()->id()) return abort(403);
        $assignment->delete();
        return back()->with('success', 'Assignment deleted.');
    }
}
