<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;

Route::post('/game/command', [GameController::class, 'command']);
Route::get('/game/state', [GameController::class, 'state']);
