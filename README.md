# Terminal Quest

Text-based RPG styled as a retro hacker terminal. Green monochrome text on black background, CRT scanline effects, typewriter text animation, and pseudo-console command input.

Built with Laravel as the game engine and vanilla JS for the frontend.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white&style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white&style=flat-square)
![PostgreSQL](https://img.shields.io/badge/DB-PostgreSQL-4169E1?logo=postgresql&logoColor=white&style=flat-square)
![SQLite](https://img.shields.io/badge/DB-SQLite-003B57?logo=sqlite&logoColor=white&style=flat-square)

## Features

- **Retro terminal UI** — CRT scanlines, screen flicker, vignette, green phosphor glow
- **Typewriter animation** — text appears character by character
- **Command parser** — natural text commands (`/go north`, `/take key`, `/use potion`)
- **Game engine** — rooms, directional connections, locked doors, items, inventory
- **Persistent state** — player position and inventory saved in PostgreSQL (production) or SQLite (local)
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

## Deploy to Render (Free Tier)

### One-click deploy via Render Blueprint

Push to GitHub, then:

1. Go to [dashboard.render.com/blueprints](https://dashboard.render.com/blueprints)
2. Connect your GitHub repo
3. Render reads `render.yaml` and creates:
   - **Web Service** (Docker) — nginx + PHP-FPM
   - **PostgreSQL** — free 1GB database

4. Set `APP_KEY` in Render dashboard:
   ```bash
   php artisan key:generate --show
   ```
   Copy the key → Render Dashboard → Environment → `APP_KEY`

5. Deploy — migrations run automatically on each deploy via `start.sh`

6. Seed the database (first time only):
   ```bash
   # Open Render Shell and run:
   php artisan db:seed --class=GameSeeder --force
   ```

### Manual setup (if not using Blueprint)

1. Create a **Web Service (Docker)** on Render
2. Connect your GitHub repo
3. Set:
   - **Name**: `terminal-quest`
   - **Region**: Frankfurt (or closest)
   - **Branch**: `main`
   - **Plan**: Free
4. Add environment variables:
   - `APP_ENV` → `production`
   - `APP_DEBUG` → `false`
   - `APP_KEY` → (generated via `php artisan key:generate --show`)
   - `DB_CONNECTION` → `pgsql`
5. Create a **PostgreSQL** database on Render
6. Copy the `Internal Database URL` and add it as `DB_URL` env var in the web service
7. Deploy

**Note**: If you want to preserve the database after deploying, make sure to seed **after** the first deploy completes.

You can also run locally with SQLite (no PostgreSQL needed):

```bash
cp .env.example .env
# Edit .env: set DB_CONNECTION=sqlite (default)
php artisan migrate:fresh --seed
php artisan serve
```

## License

MIT
