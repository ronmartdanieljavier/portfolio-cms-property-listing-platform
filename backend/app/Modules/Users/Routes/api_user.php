<?php

use App\Modules\Users\Http\Controllers\Admin\UserController as AdminUserController;
use App\Modules\Users\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::delete('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
});

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::delete('users/{user}/force-logout', [AdminUserController::class, 'forceLogout']);
    Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus']);
});
