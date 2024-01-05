<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomItem extends Model
{
    protected $fillable = [
        'room_id',
        'item_id',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
