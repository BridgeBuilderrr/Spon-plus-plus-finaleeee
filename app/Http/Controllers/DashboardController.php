<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
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
        $pendingAssignments = Assignment::whereIn('classroom_id', $user->classrooms->pluck('id'))
                                     ->whereDoesntHave('submissions', function($q) use ($user) {
                                         $q->where('user_id', $user->id);
                                     })
                                     ->where('due_date', '>', now())
                                     ->orderBy('due_date', 'asc')
                                     ->limit(5)
                                     ->get();

        // Learning Stats
        $stats = [
            'total_classes' => $user->classrooms()->count(),
            'completed_tasks' => $user->submissions()->count(),
            'active_assignments' => Assignment::whereIn('classroom_id', $user->classrooms->pluck('id'))
                                            ->where('due_date', '>', now())
                                            ->count(),
            'member_rank' => $this->calculateRank($user),
        ];

        return view('dashboard.index', compact('recentClassrooms', 'pendingAssignments', 'stats'));
    }

    private function calculateRank($user)
    {
        $count = $user->submissions()->count();
        if ($count > 50) return 'Elite Learner';
        if ($count > 20) return 'Pro Learner';
        if ($count > 5) return 'Active Student';
        return 'Newcomer';
    }
}
