<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['classroom_id', 'title', 'description', 'open_date', 'due_date', 'files'])]
class Assignment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'open_date' => 'datetime',
            'due_date' => 'datetime',
            'files' => 'array',
        ];
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
