<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/games', [GameController::class, 'index'])
    ->middleware(['auth', 'role:admin|klant']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/games/show/{id}', [GameController::class, 'show']);
    Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
    Route::post('/games/store', [GameController::class, 'store']);
    Route::get('/games/edit/{id}', [GameController::class, 'edit']);
    Route::post('/games/update/{id}', [GameController::class, 'update']);
    Route::post('/games/destroy/{id}', [GameController::class, 'destroy']);

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('permissions', PermissionController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');

        Route::get('role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions.index');
        Route::get('role-permissions/create', [RolePermissionController::class, 'create'])->name('role-permissions.create');
        Route::post('role-permissions', [RolePermissionController::class, 'store'])->name('role-permissions.store');
        Route::get('role-permissions/{permission}/{role}/edit', [RolePermissionController::class, 'edit'])->name('role-permissions.edit');
        Route::put('role-permissions/{permission}/{role}', [RolePermissionController::class, 'update'])->name('role-permissions.update');
        Route::delete('role-permissions/{permission}/{role}', [RolePermissionController::class, 'destroy'])->name('role-permissions.destroy');

        Route::get('user-roles', [UserRoleController::class, 'index'])->name('user-roles.index');
        Route::get('user-roles/create', [UserRoleController::class, 'create'])->name('user-roles.create');
        Route::post('user-roles', [UserRoleController::class, 'store'])->name('user-roles.store');
        Route::get('user-roles/{user}/{role}/edit', [UserRoleController::class, 'edit'])->name('user-roles.edit');
        Route::put('user-roles/{user}/{role}', [UserRoleController::class, 'update'])->name('user-roles.update');
        Route::delete('user-roles/{user}/{role}', [UserRoleController::class, 'destroy'])->name('user-roles.destroy');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/geheim', function () {
    return view('geheim');
})->middleware('auth');
