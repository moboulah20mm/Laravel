<?php

use Illuminate\Support\Facades\Route;
Route::get('games', [App\Http\Controllers\GameController::class, 'index']);
Route::get('games/create', [App\Http\Controllers\GameController::class, 'create']);
Route::post('games/store', [App\Http\Controllers\GameController::class, 'store']);
Route::get('games/edit/{id}', [App\Http\Controllers\GameController::class, 'edit']);
Route::post('games/update/{id}', [App\Http\Controllers\GameController::class, 'update']);
Route::post('games/destroy/{id}', [App\Http\Controllers\GameController::class, 'destroy']);

Route::get('/', function () {
    return view('welcome');
});
