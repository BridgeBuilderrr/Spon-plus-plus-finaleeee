<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['classroom_id', 'user_id', 'title', 'content'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
