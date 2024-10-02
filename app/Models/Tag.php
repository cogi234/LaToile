<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    //Relationships

    /**
     * The posts that have this tag
     */
    public function posts() : BelongsToMany
    {
        return $this->belongsToMany(Post::class, "post_has_tags")->withPivot('indexed', 'created_at', 'updated_at');
    }

    /**
     * The posts that have this tag indexed
     */
    public function indexed_posts() : BelongsToMany
    {
        return $this->belongsToMany(Post::class, "post_has_tags")->withPivot('indexed', 'created_at', 'updated_at')
            ->wherePivot('indexed', true);
    }

    /**
     * The users that have followed this tag
     */
    public function followers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, "followed_tags");
    }

    /**
     * The users that have blocked this tag
     */
    public function blockers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, "blocked_tags");
    }
}
