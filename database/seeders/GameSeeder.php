<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Player;
use App\Models\Room;
use App\Models\RoomConnection;
use App\Models\RoomItem;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Items
        $flashlight = Item::create([
            'name' => 'Flashlight',
            'description' => 'An old but functional flashlight. Batteries included.',
            'type' => 'tool',
            'is_usable' => true,
        ]);

        $rustyKey = Item::create([
            'name' => 'Rusty Key',
            'description' => 'A rusty key. Looks like it fits an old door.',
            'type' => 'key',
            'is_usable' => true,
        ]);

        $healthPotion = Item::create([
            'name' => 'Health Potion',
            'description' => 'A small vial of red liquid. Restores 25 HP when used.',
            'type' => 'potion',
            'is_usable' => true,
        ]);

        // Rooms
        $entrance = Room::create([
            'name' => 'Building Entrance',
            'description' => 'You stand at the entrance of an abandoned building. The air smells of dust and old wiring. A flickering light buzzes overhead. Exits lead north to a corridor, east to a storage room, and a staircase goes up to the rooftop.',
            'is_start' => true,
        ]);

        $corridor = Room::create([
            'name' => 'Dark Corridor',
            'description' => 'A long corridor stretches before you. Broken fluorescent tubes hang from the ceiling. Doors line both walls, most of them jammed shut. The corridor continues north, and you can go back south to the entrance.',
            'is_start' => false,
        ]);

        $serverRoom = Room::create([
            'name' => 'Server Room',
            'description' => 'Rows of dead server racks stand like tombstones. One machine in the corner still has a faint green LED blinking. Maybe it can be powered on. The exit leads back south.',
            'is_start' => false,
        ]);

        $storage = Room::create([
            'name' => 'Storage Room',
            'description' => 'Shelves collapse under the weight of forgotten junk. Old cables, broken monitors, and a sea of dust. The exit leads back west to the entrance.',
            'is_start' => false,
        ]);

        $rooftop = Room::create([
            'name' => 'Rooftop',
            'description' => 'Cold night air hits your face. The city sprawls below, neon signs bleeding into the fog. A radio antenna towers above you. The stairs lead back down.',
            'is_start' => false,
        ]);

        // Connections
        RoomConnection::create([
            'from_room_id' => $entrance->id,
            'to_room_id' => $corridor->id,
            'direction' => 'north',
            'is_locked' => false,
        ]);

        RoomConnection::create([
            'from_room_id' => $corridor->id,
            'to_room_id' => $entrance->id,
            'direction' => 'south',
            'is_locked' => false,
        ]);

        RoomConnection::create([
            'from_room_id' => $corridor->id,
            'to_room_id' => $serverRoom->id,
            'direction' => 'north',
            'is_locked' => true,
            'required_item_id' => $rustyKey->id,
        ]);

        RoomConnection::create([
            'from_room_id' => $serverRoom->id,
            'to_room_id' => $corridor->id,
            'direction' => 'south',
            'is_locked' => false,
        ]);

        RoomConnection::create([
            'from_room_id' => $entrance->id,
            'to_room_id' => $storage->id,
            'direction' => 'east',
            'is_locked' => false,
        ]);

        RoomConnection::create([
            'from_room_id' => $storage->id,
            'to_room_id' => $entrance->id,
            'direction' => 'west',
            'is_locked' => false,
        ]);

        RoomConnection::create([
            'from_room_id' => $entrance->id,
            'to_room_id' => $rooftop->id,
            'direction' => 'up',
            'is_locked' => false,
        ]);

        RoomConnection::create([
            'from_room_id' => $rooftop->id,
            'to_room_id' => $entrance->id,
            'direction' => 'down',
            'is_locked' => false,
        ]);

        // Room items
        RoomItem::create(['room_id' => $entrance->id, 'item_id' => $flashlight->id]);
        RoomItem::create(['room_id' => $storage->id, 'item_id' => $rustyKey->id]);
        RoomItem::create(['room_id' => $serverRoom->id, 'item_id' => $healthPotion->id]);

        // Default player
        Player::create([
            'name' => 'Hacker',
            'current_room_id' => $entrance->id,
            'health' => 100,
        ]);
    }
}
