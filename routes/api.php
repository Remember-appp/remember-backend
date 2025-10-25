<?php

use App\Http\Controllers\Api\AssetUploadController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me/profile',    [ProfileController::class, 'get']);
    Route::put('/me/profile',    [ProfileController::class, 'update']);
    Route::patch('/me/profile', [ProfileController::class, 'update']);

    Route::get('/me/settings',   [ProfileController::class, 'getSettings']);
    Route::put('/me/settings',   [ProfileController::class, 'updateSettings']);

    Route::post('/assets/upload',   [AssetUploadController::class, 'upload']);

    Route::get('/assets/{id}/download', [AssetUploadController::class, 'download']);
});
