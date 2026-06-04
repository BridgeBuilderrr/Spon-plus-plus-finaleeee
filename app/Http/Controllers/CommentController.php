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
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'commentable_id' => $request->input('commentable_id'),
            'commentable_type' => $request->input('commentable_type'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'content' => $comment->content,
                'user_name' => $comment->user->name,
                'avatar' => "https://ui-avatars.com/api/?name=" . urlencode($comment->user->name) . "&background=6366f1&color=fff"
            ]);
        }

        return back()->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
