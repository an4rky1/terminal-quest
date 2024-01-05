<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    protected $fillable = [
        'name',
        'current_room_id',
        'health',
    ];

    protected $casts = [
        'health' => 'integer',
    ];

    public function currentRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'current_room_id');
    }

    public function inventory(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'player_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
