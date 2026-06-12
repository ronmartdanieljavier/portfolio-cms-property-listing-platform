<?php

use App\Modules\Properties\Http\Controllers\PropertyAmenityController;
use App\Modules\Properties\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::prefix('properties')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PropertyController::class, 'index']);
    Route::post('/', [PropertyController::class, 'store']);
    Route::get('/{id}', [PropertyController::class, 'show']);
    Route::put('/{id}', [PropertyController::class, 'update']);
    Route::patch('/{id}', [PropertyController::class, 'update']);
    Route::delete('/{id}', [PropertyController::class, 'destroy']);

    Route::prefix('/{propertyId}/amenities')->group(function () {
        Route::post('/', [PropertyAmenityController::class, 'store']);
        Route::put('/', [PropertyAmenityController::class, 'update']);
        Route::patch('/', [PropertyAmenityController::class, 'update']);
        Route::delete('/{amenityId}', [PropertyAmenityController::class, 'destroy']);
    });
});
