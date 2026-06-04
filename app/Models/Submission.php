<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['assignment_id', 'user_id', 'content', 'files', 'status', 'grade', 'teacher_comment', 'graded_at', 'submitted_at'])]
class Submission extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'graded_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
