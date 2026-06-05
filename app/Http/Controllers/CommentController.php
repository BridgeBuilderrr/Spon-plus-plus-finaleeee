<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string',
            'parent_id' => 'nullable|integer|exists:comments,id'
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'commentable_id' => $request->input('commentable_id'),
            'commentable_type' => $request->input('commentable_type'),
            'parent_id' => $request->input('parent_id'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'id' => $comment->id,
                'content' => $comment->content,
                'user_name' => $comment->user->name,
                'username' => $comment->user->username,
                'user_id' => $comment->user_id,
                'created_at' => 'Just now',
                'avatar' => $comment->user->avatar_path ? asset('storage/'.$comment->user->avatar_path) : "https://ui-avatars.com/api/?name=" . urlencode($comment->user->name) . "&background=6366f1&color=fff"
            ]);
        }

        return back()->with('success', 'Comment added.');
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['content' => 'required|string']);
        $comment->update(['content' => $request->input('content')]);

        return response()->json(['success' => true, 'content' => $comment->content]);
    }

    public function destroy(Comment $comment)
    {
        $user = Auth::user();
        $isOwner = $comment->user_id === $user->id;
        
        // Find classroom teacher
        $classroom = $comment->commentable->classroom;
        $isTeacher = $classroom && $classroom->teacher_id === $user->id;

        if (!$isOwner && !$isTeacher) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->with('error', 'Unauthorized.');
        }

        $comment->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment deleted.');
    }
}
