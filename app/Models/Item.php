<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'is_usable',
    ];

    protected $casts = [
        'is_usable' => 'boolean',
    ];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_items')->withTimestamps();
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function lockedConnections()
    {
        return $this->hasMany(RoomConnection::class, 'required_item_id');
    }
}
