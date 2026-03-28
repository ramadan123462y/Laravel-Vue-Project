<?php

use App\Http\Controllers\AdminDashboard\RoomController;
use App\Http\Controllers\AdminDashboard\ClientController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// ======================== Rooms ====================
Route::prefix('rooms')->as('rooms.')->group(function () {
    Route::get('/',              [RoomController::class, 'index'])->name('index');
    Route::get('/create',        [RoomController::class, 'create'])->name('create');
    Route::post('/',             [RoomController::class, 'store'])->name('store');
    Route::get('/{room}/edit',   [RoomController::class, 'edit'])->name('edit');
    Route::post('/{room}',       [RoomController::class, 'update'])->name('update');
    Route::delete('/{room}',     [RoomController::class, 'destroy'])->name('destroy');
});

// ======================== Manage Clients ====================

Route::middleware(['auth', 'role:admin|manager|receptionist'])

    ->group(function () {

        Route::prefix('clients')->name('clients.')->group(function () {
            Route::get('/',                   [ClientController::class, 'index'])->name('index');
            Route::get('/create',             [ClientController::class, 'create'])->name('create');
            Route::post('/',                  [ClientController::class, 'store'])->name('store');
            Route::get('/approved',           [ClientController::class, 'approvedClients'])->name('approved');
            Route::get('/{client}/edit',      [ClientController::class, 'edit'])->name('edit');
            Route::post('/{client}/update', [ClientController::class, 'update'])->name('update');
            Route::delete('/{client}',        [ClientController::class, 'destroy'])->name('destroy');
            Route::patch('/{client}/approve', [ClientController::class, 'approve'])->name('approve');
        });

    });
