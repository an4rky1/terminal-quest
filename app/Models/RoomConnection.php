<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomConnection extends Model
{
    protected $fillable = [
        'from_room_id',
        'to_room_id',
        'direction',
        'is_locked',
        'required_item_id',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function fromRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'from_room_id');
    }

    public function toRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'to_room_id');
    }

    public function requiredItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'required_item_id');
    }
}
