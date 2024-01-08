<?php

namespace App\Game;

use App\Models\Player;
use App\Models\RoomConnection;
use App\Models\RoomItem;
use App\Models\PlayerInventory;
use Illuminate\Support\Facades\DB;

class CommandParser
{
    private const VALID_COMMANDS = ['go', 'take', 'use', 'status', 'look', 'inventory', 'help'];

    public function handle(Player $player, string $input): array
    {
        $input = trim($input);
        $input = ltrim($input, '/');

        $parts = explode(' ', strtolower($input), 2);
        $command = $parts[0];
        $argument = $parts[1] ?? null;

        if (!in_array($command, self::VALID_COMMANDS)) {
            return [
                'success' => false,
                'message' => "Unknown command: {$command}. Type 'help' for available commands.",
            ];
        }

        return match ($command) {
            'go' => $this->handleGo($player, $argument),
            'take' => $this->handleTake($player, $argument),
            'use' => $this->handleUse($player, $argument),
            'status' => $this->handleStatus($player),
            'look' => $this->handleLook($player),
            'inventory' => $this->handleInventory($player),
            'help' => $this->handleHelp(),
            default => ['success' => false, 'message' => 'Unknown command.'],
        };
    }

    private function handleGo(Player $player, ?string $direction): array
    {
        if (!$direction) {
            return ['success' => false, 'message' => 'Go where? Specify a direction: north, south, east, west, up, down.'];
        }

        $connection = RoomConnection::where('from_room_id', $player->current_room_id)
            ->where('direction', $direction)
            ->with('toRoom', 'requiredItem')
            ->first();

        if (!$connection) {
            return ['success' => false, 'message' => "You can't go {$direction} from here."];
        }

        if ($connection->is_locked) {
            $hasKey = $player->inventory()->where('items.id', $connection->required_item_id)->exists();
            if (!$hasKey) {
                return [
                    'success' => false,
                    'message' => "The way {$direction} is locked. You need: {$connection->requiredItem->name}.",
                ];
            }
        }

        $player->update(['current_room_id' => $connection->to_room_id]);
        $player->refresh();

        $room = $player->currentRoom;
        $items = $room->items()->pluck('name')->toArray();

        return [
            'success' => true,
            'message' => "You move {$direction}.\n\n{$room->name}\n{$room->description}",
            'room_changed' => true,
            'room' => [
                'name' => $room->name,
                'description' => $room->description,
                'items' => $items,
            ],
        ];
    }

    private function handleTake(Player $player, ?string $itemName): array
    {
        if (!$itemName) {
            return ['success' => false, 'message' => 'Take what? Specify an item name.'];
        }

        $room = $player->currentRoom;
        $roomItem = RoomItem::where('room_id', $room->id)
            ->join('items', 'room_items.item_id', '=', 'items.id')
            ->where('items.name', 'like', "%{$itemName}%")
            ->select('room_items.*', 'items.name as item_name', 'items.id as item_id')
            ->first();

        if (!$roomItem) {
            return ['success' => false, 'message' => "There is no '{$itemName}' here."];
        }

        DB::transaction(function () use ($player, $roomItem) {
            RoomItem::where('id', $roomItem->id)->delete();

            $existing = PlayerInventory::where('player_id', $player->id)
                ->where('item_id', $roomItem->item_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity');
            } else {
                PlayerInventory::create([
                    'player_id' => $player->id,
                    'item_id' => $roomItem->item_id,
                    'quantity' => 1,
                ]);
            }
        });

        return [
            'success' => true,
            'message' => "You picked up: {$roomItem->item_name}.",
        ];
    }

    private function handleUse(Player $player, ?string $itemName): array
    {
        if (!$itemName) {
            return ['success' => false, 'message' => 'Use what? Specify an item name.'];
        }

        $inventoryItem = PlayerInventory::where('player_id', $player->id)
            ->join('items', 'player_inventory.item_id', '=', 'items.id')
            ->where('items.name', 'like', "%{$itemName}%")
            ->select('player_inventory.*', 'items.name as item_name', 'items.type as item_type', 'items.id as item_id', 'items.is_usable')
            ->first();

        if (!$inventoryItem) {
            return ['success' => false, 'message' => "You don't have '{$itemName}'."];
        }

        if (!$inventoryItem->is_usable) {
            return ['success' => false, 'message' => "You can't use {$inventoryItem->item_name}."];
        }

        if ($inventoryItem->item_type === 'potion') {
            $newHealth = min(100, $player->health + 25);
            $healed = $newHealth - $player->health;

            DB::transaction(function () use ($player, $inventoryItem) {
                if ($inventoryItem->quantity > 1) {
                    $inventoryItem->decrement('quantity');
                } else {
                    PlayerInventory::where('id', $inventoryItem->id)->delete();
                }
            });

            $player->update(['health' => $newHealth]);

            return [
                'success' => true,
                'message' => "You used {$inventoryItem->item_name}. Restored {$healed} HP. (Current: {$newHealth}/100)",
            ];
        }

        return [
            'success' => false,
            'message' => "You're not sure how to use {$inventoryItem->item_name} right now.",
        ];
    }

    private function handleStatus(Player $player): array
    {
        $room = $player->currentRoom;
        $connections = RoomConnection::where('from_room_id', $room->id)->pluck('direction')->toArray();
        $items = $room->items()->pluck('name')->toArray();

        return [
            'success' => true,
            'message' => "Location: {$room->name}\nHealth: {$player->health}/100\nExits: " . implode(', ', $connections) . ($items ? "\nItems here: " . implode(', ', $items) : ''),
        ];
    }

    private function handleLook(Player $player): array
    {
        $room = $player->currentRoom;
        $connections = RoomConnection::where('from_room_id', $room->id)->pluck('direction')->toArray();
        $items = $room->items()->pluck('name')->toArray();

        $output = "{$room->name}\n{$room->description}\n\nExits: " . implode(', ', $connections);
        if ($items) {
            $output .= "\nYou see: " . implode(', ', $items);
        }

        return ['success' => true, 'message' => $output];
    }

    private function handleInventory(Player $player): array
    {
        $items = $player->inventory()->get();

        if ($items->isEmpty()) {
            return ['success' => true, 'message' => 'Your inventory is empty.'];
        }

        $lines = $items->map(function ($item) {
            $qty = $item->pivot->quantity > 1 ? " (x{$item->pivot->quantity})" : '';
            return "- {$item->name}{$qty}: {$item->description}";
        });

        return ['success' => true, 'message' => "Inventory:\n" . $lines->join("\n")];
    }

    private function handleHelp(): array
    {
        return [
            'success' => true,
            'message' => "Available commands:\n  /go <direction>  - Move (north, south, east, west, up, down)\n  /take <item>     - Pick up an item\n  /use <item>      - Use an item from inventory\n  /status          - Check current status\n  /look            - Look around the room\n  /inventory       - Check your inventory\n  /help            - Show this help",
        ];
    }
}
