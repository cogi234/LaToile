<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Events\UserDeleting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        "bio",
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

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'deleting' => UserDeleting::class,
    ];

    //Random functions

    public function getAvatar()
    {
        if ($this->avatar == null || $this->avatar == '')
            return "/images/no-avatar.png";
        else
            return Storage::url($this->avatar);
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
    
    /**
     * My reports
     */
    public function reports() : HasMany
    {
        return $this->hasMany(Report::class);
    }
    
    /**
     * My warnings
     */
    public function warnings() : HasMany
    {
        return $this->hasMany(Warning::class);
    }
    
    /**
     * My bans
     */
    public function bans() : HasMany
    {
        return $this->hasMany(Ban::class);
    }
}
