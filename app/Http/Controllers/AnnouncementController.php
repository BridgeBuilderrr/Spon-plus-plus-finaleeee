<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Notifications\ClassroomActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $announcement = $classroom->announcements()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

        Notification::send($classroom->users, new ClassroomActivityNotification($classroom, $announcement, 'announcement'));

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
