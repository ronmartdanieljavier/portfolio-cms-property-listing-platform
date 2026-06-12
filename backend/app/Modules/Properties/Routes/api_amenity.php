<?php

use App\Modules\Properties\Http\Controllers\AmenityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/amenities', [AmenityController::class, 'index']);
});
