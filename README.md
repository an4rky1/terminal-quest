# Terminal Quest

Text-based RPG styled as a retro hacker terminal. Green monochrome text on black background, CRT scanline effects, typewriter text animation, and pseudo-console command input.

Built with Laravel as the game engine and vanilla JS for the frontend.

![Terminal Quest](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white&style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white&style=flat-square)
![SQLite](https://img.shields.io/badge/DB-SQLite-003B57?logo=sqlite&logoColor=white&style=flat-square)

## Features

- **Retro terminal UI** — CRT scanlines, screen flicker, vignette, green phosphor glow
- **Typewriter animation** — text appears character by character
- **Command parser** — natural text commands (`/go north`, `/take key`, `/use potion`)
- **Game engine** — rooms, directional connections, locked doors, items, inventory
- **Persistent state** — player position and inventory saved in SQLite
- **API-driven** — frontend communicates with backend via JSON API

## Commands

| Command | Description |
|---|---|
| `/go <direction>` | Move (north, south, east, west, up, down) |
| `/take <item>` | Pick up an item from the current room |
| `/use <item>` | Use an item from inventory |
| `/look` | Look around the current room |
| `/status` | Check health, location, exits, and items |
| `/inventory` | View your inventory |
| `/help` | Show available commands |

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup environment and database
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# Build frontend assets
npm run build

# Start the server
php artisan serve
```

Open **http://127.0.0.1:8000** in your browser.

## Development

Run Vite dev server with hot module replacement:

```bash
npm run dev
```

Run Laravel server in another terminal:

```bash
php artisan serve
```

## Project Structure

```
app/
├── Game/
│   └── CommandParser.php        # Game command handler
├── Http/Controllers/Api/
│   └── GameController.php       # API endpoints
└── Models/
    ├── Item.php                 # Item definitions
    ├── Player.php               # Player session
    ├── PlayerInventory.php      # Player inventory
    ├── Room.php                 # Game locations
    ├── RoomConnection.php       # Directional transitions
    └── RoomItem.php             # Items placed in rooms

database/
└── seeders/
    └── GameSeeder.php           # Starter map (5 rooms)

resources/
├── css/app.css                  # Terminal styles + CRT effects
├── js/app.js                    # Terminal UI + API integration
└── views/terminal.blade.php     # Main game view

routes/
├── api.php                      # POST /api/game/command, GET /api/game/state
└── web.php                      # Serves the terminal view
```

## Database Schema

```
rooms              — id, name, description, is_start
room_connections   — id, from_room_id, to_room_id, direction, is_locked, required_item_id
items              — id, name, description, type, is_usable
room_items         — id, room_id, item_id  (items on the floor)
players            — id, name, current_room_id, health
player_inventory   — id, player_id, item_id, quantity
```

## Starter Map

```
        [Server Room] (locked — needs Rusty Key)
              |
        [Dark Corridor]
              |
[Storage] -- [Entrance] -- [Rooftop]
 (key)        (start)
              (flashlight)
```

## API

### Send a command

```
POST /api/game/command
Content-Type: application/json

{
  "command": "go north",
  "player_id": 1  // optional, auto-created if omitted
}
```

### Get player state

```
GET /api/game/state?player_id=1
```

## License

MIT
