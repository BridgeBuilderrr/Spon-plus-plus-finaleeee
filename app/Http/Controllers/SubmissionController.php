<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function store(Request $request, \App\Models\Classroom $classroom, Assignment $assignment)
    {
        $request->validate([
            'content' => 'nullable|string',
            'files' => 'nullable|array|max:10',
            'answers' => 'nullable|array',
        ]);

        if ($assignment->submissions()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already submitted this assignment.');
        }

        if ($assignment->due_date && $assignment->due_date->isPast()) {
            // Optional: allow late submissions but mark them
        }

        $grade = null;
        $status = 'submitted';
        $teacherComment = null;
        $gradedAt = null;

        if ($assignment->assignment_type === 'pilihan_ganda' && is_array($assignment->questions)) {
            $questions = $assignment->questions;
            $totalQuestions = count($questions);
            if ($totalQuestions > 0) {
                $autoGradableCount = 0;
                $correctCount = 0;
                $submittedAnswers = $request->input('answers', []);
                foreach ($questions as $index => $question) {
                    $qType = $question['type'] ?? 'multiple_choice';
                    if ($qType === 'text') {
                        continue;
                    }
                    $autoGradableCount++;
                    if ($qType === 'checkboxes') {
                        // Checkbox questions allow selecting multiple answers.
                        // Both should be arrays of integers.
                        $correctIndices = isset($question['correct']) ? (array)$question['correct'] : [];
                        $correctIndices = array_map('intval', $correctIndices);
                        sort($correctIndices);

                        $submitted = isset($submittedAnswers[$index]) ? (array)$submittedAnswers[$index] : [];
                        $submitted = array_map('intval', $submitted);
                        sort($submitted);

                        if ($submitted === $correctIndices) {
                            $correctCount++;
                        }
                    } else {
                        // Single-select Multiple Choice questions.
                        // Correct index can be array (if switched types, take first) or single integer.
                        $correctIndex = -1;
                        if (isset($question['correct'])) {
                            if (is_array($question['correct'])) {
                                $correctIndex = count($question['correct']) > 0 ? intval($question['correct'][0]) : -1;
                            } else {
                                $correctIndex = intval($question['correct']);
                            }
                        }

                        $studentIndex = -2;
                        if (isset($submittedAnswers[$index])) {
                            if (is_array($submittedAnswers[$index])) {
                                $studentIndex = count($submittedAnswers[$index]) > 0 ? intval($submittedAnswers[$index][0]) : -2;
                            } else {
                                $studentIndex = intval($submittedAnswers[$index]);
                            }
                        }

                        if ($studentIndex === $correctIndex) {
                            $correctCount++;
                        }
                    }
                }
                
                if ($autoGradableCount > 0) {
                    $grade = round(($correctCount / $autoGradableCount) * 100, 2);
                    $status = 'graded';
                    $teacherComment = 'Sistem: Dinilai Otomatis (' . $correctCount . '/' . $autoGradableCount . ' benar)';
                    $gradedAt = now();
                } else {
                    $status = 'submitted';
                }
            }
        }

        $assignment->submissions()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'answers' => $request->input('answers') ?? [],
            'files' => $request->input('files') ?? [],
            'status' => $status,
            'grade' => $grade,
            'teacher_comment' => $teacherComment,
            'graded_at' => $gradedAt,
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Assignment submitted successfully!');
    }

    public function update(Request $request, \App\Models\Classroom $classroom, Assignment $assignment, Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'nullable|string',
            'files' => 'nullable|array|max:10',
        ]);

        $submission->update([
            'content' => $request->input('content'),
            'files' => $request->input('files') ?? [],
        ]);

        return back()->with('success', 'Submission updated successfully!');
    }

    public function destroy(\App\Models\Classroom $classroom, Assignment $assignment, Submission $submission)
    {
        if ($submission->user_id !== Auth::id()) {
            abort(403);
        }

        $submission->delete();
        return back()->with('success', 'Submission removed.');
    }
}
