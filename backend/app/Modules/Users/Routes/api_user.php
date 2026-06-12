<?php

use App\Modules\Users\Http\Controllers\Admin\UserController as AdminUserController;
use App\Modules\Users\Http\Controllers\Auth\AuthController;
use App\Modules\Users\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::delete('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
});

Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::patch('/', [ProfileController::class, 'update']);
});

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('users', [AdminUserController::class, 'index']);
    Route::post('users', [AdminUserController::class, 'store']);
    Route::patch('users/{user}', [AdminUserController::class, 'update']);
    Route::delete('users/{user}', [AdminUserController::class, 'destroy']);
    Route::delete('users/{user}/force-logout', [AdminUserController::class, 'forceLogout']);
    Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus']);
});
