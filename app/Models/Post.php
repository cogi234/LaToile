<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'previous_content',
        'user_id',
        'original',
        'previous'
    ];

    //Relationships

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The original post we are a share of/response to
     */
    public function original() : BelongsTo
    {
        return $this->belongsTo(Post::class, "original_id", "id");
    }

    /**
     * The posts which are shares of/responses to this post
     */
    public function shares() : HasMany
    {
        return $this->hasMany(Post::class, "original_id", "id");
    }

    /**
     * The post we are a share of/response to
     */
    public function previous() : BelongsTo
    {
        return $this->belongsTo(Post::class, "previous_id");
    }

    /**
     * The posts which are direct shares of/responses to this post
     */
    public function direct_shares() : HasMany
    {
        return $this->hasMany(Post::class, "previous_id");
    }

    /**
     * The users that have liked this post
     */
    public function likes() :BelongsToMany
    {
        return $this->belongsToMany(User::class, "likes");
    }

    /**
     * The tags on this post
     */
    public function tags() :BelongsToMany
    {
        return $this->belongsToMany(Tag::class, "post_has_tags");
    }
}
