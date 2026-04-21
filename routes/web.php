<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('rooms')->name('rooms.')->group(function () {
    Route::get('/', [RoomController::class, 'index'])->name('index');
    Route::post('/{room}/start', [RoomController::class, 'startSession'])->name('start-session');
    Route::post('/sessions/{session}/extend', [RoomController::class, 'extendSession'])->name('extend-session');
    Route::post('/sessions/{session}/bill-out', [RoomController::class, 'billOut'])->name('bill-out');
});

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::post('/add-item', [OrderController::class, 'addItem'])->name('add-item');
    Route::post('/update-quantity/{item}', [OrderController::class, 'updateQuantity'])->name('update-quantity');
    Route::post('/remove-item/{item}', [OrderController::class, 'removeItem'])->name('remove-item');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/clear', [OrderController::class, 'clear'])->name('clear');
});

Route::prefix('menu')->name('menu.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::post('/store', [MenuController::class, 'store'])->name('store');
    Route::match(['put', 'patch'], '/{item}', [MenuController::class, 'update'])->name('update');
    Route::delete('/{item}', [MenuController::class, 'destroy'])->name('destroy');
});

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::patch('/update', [ProfileController::class, 'update'])->name('update');
    Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password');
});
