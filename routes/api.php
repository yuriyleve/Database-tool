<?php

use App\Http\Controllers\PokerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api')->group(function () {
    Route::resource('/poker', PokerController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('/poker/blacklist', [PokerController::class, 'blacklist']);
    Route::post('/poker/timer', [PokerController::class, 'timer']);
    Route::post('/poker/isTime', [PokerController::class, 'isTime']);
    Route::post('/poker/createPlayers', [PokerController::class, 'createPlayers']);
    Route::post('/poker/resetAllTime', [PokerController::class, 'resetAllTime']);
    Route::post('/poker/stopAllTimer', [PokerController::class, 'stopAllTimer']);
});