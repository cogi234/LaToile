<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'moderator',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    //Relationships

    /**
     * The users I follow
     */
    public function follows() : BelongsToMany
    {
        return $this->belongsToMany(User::class, "followed_users", "user", "followed");
    }
    
    /**
     * The users that follow me
     */
    public function followers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, "followed_users", "followed", "user");
    }

    /**
     * The users I have blocked
     */
    public function blocks() : BelongsToMany
    {
        return $this->belongsToMany(User::class, "blocked_users", "user", "blocked");
    }

    /**
     * The users that have blocked me
     */
    public function blockers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, "blocked_users", "blocked", "user");
    }
}
