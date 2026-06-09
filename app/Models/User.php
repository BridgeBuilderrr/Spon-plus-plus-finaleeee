<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'profile_photo', 'profile_banner', 'bio', 'session_id', 'last_seen_at', 'avatar_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Get the user's avatar path, mapping it to the profile_photo database column.
     */
    public function getAvatarPathAttribute()
    {
        return $this->profile_photo;
    }

    /**
     * Set the user's avatar path, mapping it to the profile_photo database column.
     */
    public function setAvatarPathAttribute($value)
    {
        $this->attributes['profile_photo'] = $value;
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class)
                    ->withPivot('role', 'is_starred', 'last_accessed_at')
                    ->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
