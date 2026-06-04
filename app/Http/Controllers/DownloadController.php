<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    /**
     * Handle material or assignment file download
     */
    public function download(Request $request)
    {
        $path = $request->query('path');
        $assignmentId = $request->query('assignment_id');

        if (!$path) abort(404);

        // If it's a student (member) downloading an assignment file, 
        // mark it as done automatically.
        if ($assignmentId && Auth::check() && Auth::user()->role === 'member') {
            Submission::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'assignment_id' => $assignmentId,
                ],
                [
                    'status' => 'done',
                    'submitted_at' => now(),
                ]
            );
        }

        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);

        return response()->download($fullPath);
    }
}
