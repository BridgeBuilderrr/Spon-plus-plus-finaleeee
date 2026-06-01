<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $classroom->announcements()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Announcement shared!');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}
