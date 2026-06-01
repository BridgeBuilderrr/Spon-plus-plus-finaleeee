<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Http\Request;
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

        $classroom->materials()->create([
            'title' => $request->title,
            'description' => $request->description,
            'files' => $request->files,
        ]);

        return redirect()->route('courses.show', $classroom)->with('success', 'Material uploaded successfully!');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB limit
        ]);

        $path = $request->file('file')->store('materials', 'public');

        return response()->json([
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'size' => $request->file('file')->getSize(),
        ]);
    }
}
