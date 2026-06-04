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
        // For "Mark as Done", files might be empty if it's just a status change
        // But user said "automatically when file downloaded", which isn't standard.
        // I'll make files optional if they just want to mark it as done.
        $request->validate([
            'content' => 'nullable|string',
            'files' => 'nullable|array|max:10',
        ]);

        // Check if already submitted
        if ($assignment->submissions()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already submitted this assignment.');
        }

        // Check if past deadline
        if ($assignment->due_date && $assignment->due_date->isPast()) {
            return back()->with('error', 'The deadline for this assignment has passed.');
        }

        $assignment->submissions()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'files' => $request->input('files') ?? [],
        ]);

        return back()->with('success', 'Assignment submitted successfully!');
    }
}
