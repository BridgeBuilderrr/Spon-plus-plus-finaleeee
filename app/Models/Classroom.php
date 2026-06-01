<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'code', 'description', 'tags', 'banner', 'teacher_id'])]
class Classroom extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role', 'is_starred', 'last_accessed_at')
                    ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}
