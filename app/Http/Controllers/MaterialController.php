<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Material;
use App\Notifications\ClassroomActivityNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'files' => 'required|array', // Array of file names already uploaded via fine-uploader/dropzone
        ]);

        $material = $classroom->materials()->create([
            'title' => $request->title,
            'description' => $request->description,
            'files' => $request->input('files', []),
        ]);

        Notification::send($classroom->users, new ClassroomActivityNotification($classroom, $material, 'material'));

        return redirect()->route('courses.show', $classroom)->with('success', 'Material uploaded successfully!');
    }

    public function update(Request $request, Classroom $classroom, \App\Models\Material $material)
    {
        if ($classroom->teacher_id !== auth()->id()) return abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $material->update([
            'title' => $request->title,
            'description' => $request->description,
            'files' => $request->input('files', $material->files)
        ]);

        return back()->with('success', 'Material updated!');
    }

    public function destroy(Classroom $classroom, Material $material)
    {
        if ($classroom->teacher_id !== auth()->id()) return abort(403);
        $material->delete();
        return back()->with('success', 'Material deleted.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB limit
        ]);

        $path = $request->file('file')->store('materials', 'public');

        return response()->json([
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'size' => $request->file('file')->getSize(),
        ]);
    }
}
