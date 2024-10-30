<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Events\UserCreated;
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
        'created' => UserCreated::class
    ];

    //Random functions

    public function getAvatar()
    {
        if ($this->avatar == null || $this->avatar == '')
            return "/images/no-avatar.png";
        else
            return Storage::url($this->avatar);
    }

    public function isBanned() 
    {
        $bans = $this->bans()->get();
        foreach ($bans as $ban) {
            if ($ban->end_time === null || $ban->end_time > now())
            return true;
        }
        return false;
    }

    public function unban()
    {
        $bans = $this->bans()->get();
        foreach ($bans as $ban) {
            if ($ban->end_time === null || $ban->end_time > now())
                $ban->delete();
        }
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
     * My drafts
     */
    public function drafts() : HasMany
    {
        return $this->hasMany(Draft::class);
    }

    /**
     * My queued posts
     */
    public function queued_posts() : HasMany
    {
        return $this->hasMany(QueuedPost::class);
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
    
    /**
     * My sent private messages
     */
    public function sent_private_messages() : HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }
    
    /**
     * My received private messages
     */
    public function received_private_messages() : HasMany
    {
        return $this->hasMany(PrivateMessage::class, 'receiver_id');
    }
    
    /**
     * All my private messages, received and sent
     */
    public function all_private_messages() : HasMany
    {
        return $this->sent_private_messages()->union($this->received_private_messages()->getQuery());
    }
    
    /**
     * My pending group invites
     */
    public function group_invites() : BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_memberships')->wherePivot('status', 'invite');
    }
    
    /**
     * My group memberships
     */
    public function group_memberships() : BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_memberships')->wherePivot('status', '!=', 'invite');
    }
    
    /**
     * My sent group messages
     */
    public function sent_group_messages() : HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }
}
