<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name', 'description', 'join_code'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'class_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function teachers()
    {
        return $this->users()->wherePivot('role', 'teacher');
    }

    public function members()
    {
        return $this->users()->wherePivot('role', 'member');
    }
}
