<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;

    
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
        return $this->belongsTo(Post::class, "original");
    }

    /**
     * The posts which are shares of/responses to this post
     */
    public function shares() : HasMany
    {
        return $this->hasMany(Post::class, "original");
    }

    /**
     * The post we are a share of/response to
     */
    public function previous() : BelongsTo
    {
        return $this->belongsTo(Post::class, "previous");
    }

    /**
     * The posts which are direct shares of/responses to this post
     */
    public function direct_shares() : HasMany
    {
        return $this->hasMany(Post::class, "previous");
    }
}
