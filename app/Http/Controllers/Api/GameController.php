<?php

namespace App\Http\Controllers\Api;

use App\Game\CommandParser;
use App\Models\Player;
use App\Models\RoomConnection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function command(Request $request, CommandParser $parser)
    {
        $request->validate([
            'command' => 'required|string|max:255',
            'player_id' => 'nullable|integer|exists:players,id',
        ]);

        $player = $this->getOrCreatePlayer($request->input('player_id'));

        $result = $parser->handle($player, $request->input('command'));

        return response()->json(array_merge($result, [
            'player_id' => $player->id,
            'state' => $this->buildState($player),
        ]));
    }

    public function state(Request $request)
    {
        $request->validate([
            'player_id' => 'required|integer|exists:players,id',
        ]);

        $player = Player::with('currentRoom.items')->findOrFail($request->input('player_id'));

        return response()->json([
            'success' => true,
            'state' => $this->buildState($player),
        ]);
    }

    private function getOrCreatePlayer(?int $id): Player
    {
        if ($id) {
            return Player::findOrFail($id);
        }

        return Player::firstOrCreate(
            ['name' => 'Hacker'],
            [
                'current_room_id' => fn() => \App\Models\Room::where('is_start', true)->first()?->id,
                'health' => 100,
            ]
        );
    }

    private function buildState(Player $player): array
    {
        $room = $player->currentRoom;
        $exits = RoomConnection::where('from_room_id', $room->id)->pluck('direction')->toArray();
        $floorItems = $room->items()->get()->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
        ]);

        $inventory = $player->inventory()->get()->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'quantity' => $item->pivot->quantity,
        ]);

        return [
            'player_id' => $player->id,
            'health' => $player->health,
            'room' => [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
            ],
            'exits' => $exits,
            'floor_items' => $floorItems,
            'inventory' => $inventory,
        ];
    }
}
