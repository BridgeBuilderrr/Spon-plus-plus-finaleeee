<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function store(Request $request, Assignment $assignment)
    {
        $request->validate([
            'content' => 'nullable|string',
            'files' => 'required|array|min:1|max:10',
        ]);

        // Check if already submitted
        if ($assignment->submissions()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already submitted this assignment.');
        }

        // Check if past deadline
        if ($assignment->due_date->isPast()) {
            return back()->with('error', 'The deadline for this assignment has passed.');
        }

        $assignment->submissions()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'files' => $request->input('files'),
        ]);

        return back()->with('success', 'Assignment submitted successfully!');
    }
}
