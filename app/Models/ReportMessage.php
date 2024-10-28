<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReportMessage extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reason',
        'message_id',
        'message_type',
    ];

    
    //Relationships

    /**
     * Définir la relation polymorphique avec les messages.
     */
    public function message(): MorphTo
    {
        return $this->morphTo();
    }

        /**
     * Get all bans associated with this report message.
     */
    public function bans(): MorphMany
    {
        return $this->morphMany(Ban::class, 'report');
    }
}
