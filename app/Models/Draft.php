<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Draft extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'previous_id'
    ];
    /**
     * Some attributes get cast
     */
    protected function casts(): array
    {
        return [
            'content' => 'array'
        ];
    }

    //Relationships

    /**
     * The user who published this post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The post we are a share of/response to
     */
    public function previous(): BelongsTo
    {
        return $this->belongsTo(Post::class, "previous_id");
    }
}
