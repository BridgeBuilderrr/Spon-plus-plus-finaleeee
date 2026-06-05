<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->get('q');
        if (!$q) {
            return view('search.results', [
                'query' => '',
                'classrooms' => collect(),
                'assignments' => collect(),
                'users' => collect(),
            ]);
        }

        $results = [
            'query' => $q,
            'classrooms' => collect(),
            'assignments' => collect(),
            'users' => collect(),
        ];

        // Parse special tags
        $type = null;
        $cleanQuery = $q;

        if (preg_match('/^(people|user|person):(.*)$/i', $q, $matches)) {
            $type = 'users';
            $cleanQuery = trim($matches[2]);
        } elseif (preg_match('/^(class|space|classroom):(.*)$/i', $q, $matches)) {
            $type = 'classrooms';
            $cleanQuery = trim($matches[2]);
        } elseif (preg_match('/^(assignment|task):(.*)$/i', $q, $matches)) {
            $type = 'assignments';
            $cleanQuery = trim($matches[2]);
        }

        if ($type === 'users' || $type === null) {
            $results['users'] = User::where('name', 'like', "%{$cleanQuery}%")
                ->orWhere('username', 'like', "%{$cleanQuery}%")
                ->limit(20)
                ->get();
        }

        if ($type === 'classrooms' || $type === null) {
            $results['classrooms'] = Classroom::with('teacher')
                ->where('title', 'like', "%{$cleanQuery}%")
                ->orWhere('description', 'like', "%{$cleanQuery}%")
                ->orWhere('tags', 'like', "%{$cleanQuery}%")
                ->limit(20)
                ->get();
        }

        if ($type === 'assignments' || $type === null) {
            $results['assignments'] = Assignment::with('classroom')
                ->where('title', 'like', "%{$cleanQuery}%")
                ->orWhere('description', 'like', "%{$cleanQuery}%")
                ->limit(20)
                ->get();
        }

        return view('search.results', $results);
    }
}
