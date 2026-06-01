<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Recently accessed classrooms
        $recentClassrooms = $user->classrooms()
                                ->orderByPivot('last_accessed_at', 'desc')
                                ->limit(4)
                                ->get();

        // Pending assignments (nearest due date)
        // Only for Member role in those classes
        $pendingAssignments = Assignment::whereIn('classroom_id', $user->classrooms->pluck('id'))
                                     ->where('due_date', '>', now())
                                     ->orderBy('due_date', 'asc')
                                     ->limit(5)
                                     ->get();

        return view('dashboard.index', compact('recentClassrooms', 'pendingAssignments'));
    }
}
