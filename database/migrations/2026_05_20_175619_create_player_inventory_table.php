<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['player_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_inventory');
    }
};
