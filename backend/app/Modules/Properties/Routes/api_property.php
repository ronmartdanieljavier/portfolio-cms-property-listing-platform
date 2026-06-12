<?php

use App\Modules\Properties\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::prefix('properties')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [PropertyController::class, 'store']);
});
