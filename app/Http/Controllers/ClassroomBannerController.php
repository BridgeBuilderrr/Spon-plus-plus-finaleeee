<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClassroomBannerController extends Controller
{
    /**
     * Authorize that the current user is a teacher for the classroom.
     */
    protected function authorizeTeacher(Classroom $classroom)
    {
        $role = $classroom->users()
            ->where('user_id', Auth::id())
            ->first()
            ->pivot->role ?? null;

        if ($role !== 'teacher') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function update(Request $request, Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'banner' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);

        if ($classroom->banner_path) {
            $oldPath = public_path($classroom->banner_path);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file = $request->file('banner');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/banners'), $fileName);
        
        $classroom->update(['banner_path' => 'uploads/banners/' . $fileName]);

        return back()->with('success', 'Banner updated.');
    }

    public function destroy(Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        if ($classroom->banner_path) {
            $oldPath = public_path($classroom->banner_path);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $classroom->update(['banner_path' => null]);

        return back()->with('success', 'Banner removed.');
    }
}
