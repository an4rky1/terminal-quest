<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('to_room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('direction'); // north, south, east, west, up, down
            $table->boolean('is_locked')->default(false);
            $table->foreignId('required_item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->timestamps();

            $table->unique(['from_room_id', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_connections');
    }
};
