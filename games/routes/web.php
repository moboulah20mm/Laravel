<?php

use Illuminate\Support\Facades\Route;
Route::get('games', [App\Http\Controllers\GameController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});
