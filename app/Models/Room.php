<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_start',
    ];

    protected $casts = [
        'is_start' => 'boolean',
    ];

    public function connections(): HasMany
    {
        return $this->hasMany(RoomConnection::class, 'from_room_id');
    }

    public function reverseConnections(): HasMany
    {
        return $this->hasMany(RoomConnection::class, 'to_room_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'room_items')->withTimestamps();
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'current_room_id');
    }
}
