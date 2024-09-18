<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    public function followed_users() : BelongsToMany
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
    public function blocked_users() : BelongsToMany
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

    /**
     * My posts
     */
    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * The posts I have liked
     */
    public function likes() : BelongsToMany
    {
        return $this->belongsToMany(Post::class, "likes");
    }

    /**
     * The tags I follow
     */
    public function followed_tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class, "followed_tags");
    }

    /**
     * The users that have blocked this tag
     */
    public function blocked_tags() : BelongsToMany
    {
        return $this->belongsToMany(Tag::class, "blocked_tags");
    }
}
