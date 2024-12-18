<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'user_id',
        'group_id',
    ];


    //Relationships

    /**
     * The user who sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * The group that received the message
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function reports()
    {
        return $this->morphMany(ReportMessage::class, 'message');
    }
}
