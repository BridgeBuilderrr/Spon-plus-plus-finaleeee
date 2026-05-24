<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Classroom;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Auth::user()->classrooms;
        return view('classrooms.index', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classroom = Classroom::create([
            'name' => $request->name,
            'description' => $request->description,
            'join_code' => strtoupper(Str::random(8)),
        ]);

        $classroom->users()->attach(Auth::id(), ['role' => 'teacher']);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Class created successfully!');
    }

    public function join(Request $request)
    {
        $request->validate([
            'join_code' => 'required|string',
        ]);

        $classroom = Classroom::where('join_code', $request->join_code)->first();

        if (!$classroom) {
            return back()->withErrors(['join_code' => 'Invalid join code.']);
        }

        if ($classroom->users()->where('user_id', Auth::id())->exists()) {
            return back()->withErrors(['join_code' => 'You are already in this class.']);
        }

        $classroom->users()->attach(Auth::id(), ['role' => 'member']);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Joined class successfully!');
    }

    public function show(Classroom $classroom)
    {
        // Check if user belongs to this class
        if (!$classroom->users()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }

        $materialList = $classroom->materials()->with('uploader')->latest()->get();
        
        // Get user role in this class
        $userRole = $classroom->users()->where('user_id', Auth::id())->first()->pivot->role;

        return view('classrooms.show', compact('classroom', 'materialList', 'userRole'));
    }
}
