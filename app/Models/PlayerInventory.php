<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerInventory extends Model
{
    protected $table = 'player_inventory';

    protected $fillable = [
        'player_id',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
