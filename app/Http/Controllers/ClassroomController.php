<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query from user's classrooms
        $query = $user->classrooms();

        // Search logic (grouped to avoid affecting the user filter)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('tags', 'like', '%' . $search . '%')
                  ->orWhereHas('teacher', function($t) use ($search) {
                      $t->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Sorting
        $sort = $request->get('sort', 'name');
        if ($sort == 'name') {
            $query->orderBy('title');
        } elseif ($sort == 'last_accessed') {
            $query->orderByPivot('last_accessed_at', 'desc');
        }

        // Get classrooms with teacher information
        $classrooms = $query->with('teacher')
                           ->paginate(12)
                           ->withQueryString();

        return view('courses.index', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

        $classroom = Classroom::create([
            'title' => $request->title,
            'description' => $request->description,
            'code' => strtoupper(Str::random(7)),
            'tags' => $tags,
            'teacher_id' => Auth::id(),
        ]);

        // Attach the creator as a Teacher role in the pivot
        $classroom->users()->attach(Auth::id(), [
            'role' => 'Teacher',
            'last_accessed_at' => now()
        ]);

        return redirect()->route('courses.index')->with('success', 'Class created successfully! Code: ' . $classroom->code);
    }

    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:classrooms,code',
        ]);

        $classroom = Classroom::where('code', $request->code)->first();

        if ($classroom->users()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You are already a student of this class.');
        }

        $classroom->users()->attach(Auth::id(), [
            'role' => 'Member',
            'last_accessed_at' => now()
        ]);

        return redirect()->route('courses.index')->with('success', 'Joined class successfully!');
    }

    public function toggleStar(Classroom $classroom)
    {
        $membership = $classroom->users()->where('user_id', Auth::id())->first()->pivot;
        $membership->update(['is_starred' => !$membership->is_starred]);

        return back()->with('success', 'Class priority updated.');
    }

    public function show(Classroom $classroom)
    {
        // Update last accessed
        $classroom->users()->updateExistingPivot(Auth::id(), ['last_accessed_at' => now()]);

        // Increase sort buffer for this session to handle large activity sorting if needed
        try {
            \Illuminate\Support\Facades\DB::statement('SET SESSION sort_buffer_size = 1048576 * 4'); // 4MB
        } catch (\Exception $e) {}

        // Eager load everything needed for the stream and sidebar
        $classroom->load([
            'announcements.user', 
            'announcements.comments.user', 
            'assignments.submissions',
            'materials',
            'teacher'
        ]);

        $role = $classroom->users()->where('user_id', Auth::id())->first()->pivot->role ?? 'Member';

        return view('courses.show', compact('classroom', 'role'));
    }

    public function students(Classroom $classroom)
    {
        $teachers = $classroom->users()->wherePivot('role', 'Teacher')->get();
        $students = $classroom->users()->wherePivot('role', 'Member')->get();

        return view('courses.students', compact('classroom', 'teachers', 'students'));
    }

    public function kick(Classroom $classroom, User $user)
    {
        // Check if current user is teacher
        if ($classroom->teacher_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot kick yourself.');
        }

        $classroom->users()->detach($user->id);

        return back()->with('success', 'Student kicked successfully.');
    }

    public function exit(Classroom $classroom)
    {
        if ($classroom->teacher_id === Auth::id()) {
            return back()->with('error', 'Teachers cannot exit their own class.');
        }

        $classroom->users()->detach(Auth::id());

        return redirect()->route('courses.index')->with('success', 'You have left the class.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        if ($classroom->teacher_id !== Auth::id()) return back()->with('error', 'Unauthorized.');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

        $classroom->update([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $tags,
        ]);

        return back()->with('success', 'Class updated successfully!');
    }

    public function updateBanner(Request $request, Classroom $classroom)
    {
        if ($classroom->teacher_id !== Auth::id()) return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);

        $request->validate([
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($classroom->banner) {
                Storage::disk('public')->delete($classroom->banner);
            }

            $path = $request->file('banner')->store('banners', 'public');
            $classroom->update(['banner' => $path]);

            return response()->json(['success' => true, 'path' => asset('storage/' . $path)]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    public function deleteBanner(Classroom $classroom)
    {
        if ($classroom->teacher_id !== Auth::id()) return back()->with('error', 'Unauthorized.');

        if ($classroom->banner) {
            Storage::disk('public')->delete($classroom->banner);
            $classroom->update(['banner' => null]);
        }

        return back()->with('success', 'Banner removed.');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->teacher_id !== Auth::id()) return back()->with('error', 'Unauthorized.');

        $classroom->delete();

        return redirect()->route('courses.index')->with('success', 'Class deleted successfully.');
    }

    public function download($path)
    {
        if (!Storage::disk('public')->exists($path)) {
            return abort(404, 'File not found');
        }

        return Storage::disk('public')->download($path);
    }
}
