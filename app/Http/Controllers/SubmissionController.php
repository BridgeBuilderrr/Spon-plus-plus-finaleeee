<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function store(Request $request, \App\Models\Classroom $classroom, Assignment $assignment)
    {
        $request->validate([
            'content' => 'nullable|string',
            'files' => 'nullable|array|max:10',
        ]);

        if ($assignment->submissions()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already submitted this assignment.');
        }

        if ($assignment->due_date && $assignment->due_date->isPast()) {
            // Optional: allow late submissions but mark them
        }

        $assignment->submissions()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'files' => $request->input('files') ?? [],
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Assignment submitted successfully!');
    }

    public function update(Request $request, \App\Models\Classroom $classroom, Assignment $assignment, Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'nullable|string',
            'files' => 'nullable|array|max:10',
        ]);

        $submission->update([
            'content' => $request->input('content'),
            'files' => $request->input('files') ?? [],
        ]);

        return back()->with('success', 'Submission updated successfully!');
    }

    public function destroy(\App\Models\Classroom $classroom, Assignment $assignment, Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403);
        }

        $submission->delete();
        return back()->with('success', 'Submission removed.');
    }
}
