<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ban extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reason',
        'end_time',
        'user_id',
        'report_id',
    ];

    
    //Relationships

    /**
     * The user who got banned
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The report that lead to this ban
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
