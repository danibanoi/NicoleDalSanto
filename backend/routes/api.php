<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PublicController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

// Pubblico
Route::get('packages', [PublicController::class, 'packages']);
Route::get('packages/{slug}', [PublicController::class, 'package']);
Route::get('sessions', [PublicController::class, 'sessions']);
Route::post('contact', [PublicController::class, 'contact'])->middleware('throttle:5,1');

// Auth
Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Area cliente
    Route::get('me/dashboard', [ClientController::class, 'dashboard']);
    Route::post('me/orders', [ClientController::class, 'requestPackage']);
    Route::post('sessions/{session}/book', [ClientController::class, 'book']);
    Route::delete('sessions/{session}/book', [ClientController::class, 'cancel']);

    // Pannello Nicole
    Route::prefix('admin')->middleware(EnsureAdmin::class)->group(function () {
        Route::get('stats', Admin\DashboardController::class);
        Route::get('users', [Admin\UserController::class, 'index']);
        Route::patch('users/{user}', [Admin\UserController::class, 'update']);
        Route::get('orders', [Admin\OrderController::class, 'index']);
        Route::post('orders', [Admin\OrderController::class, 'store']);
        Route::patch('orders/{order}', [Admin\OrderController::class, 'update']);
        Route::apiResource('packages', Admin\PackageController::class)->except('show');
        Route::apiResource('sessions', Admin\SessionController::class)->except('show');
        Route::get('messages', [Admin\MessageController::class, 'index']);
        Route::patch('messages/{message}/read', [Admin\MessageController::class, 'markRead']);
    });
});
