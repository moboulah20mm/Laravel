<?php

use Illuminate\Support\Facades\Route;
Route::get('games', [App\Http\Controllers\GameController::class, 'index']);

Route::get('/', function () {
    return view('welcome');

    Route::get('games/create', [App\Http\Controllers\GameController::class, 'create']);
    Route::post('games/store', [App\Http\Controllers\GameController::class, 'store']);
});
