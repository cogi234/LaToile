<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];


    // Relationships
    
    /**
     * My pending invites
     */
    public function invites() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_memberships')->wherePivot('status', 'invite');
    }
    
    /**
     * My memberships
     */
    public function memberships() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_memberships')->wherePivot('status', '!=', 'invite');
    }
    
    /**
     * My messages
     */
    public function messages() : HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }
}
