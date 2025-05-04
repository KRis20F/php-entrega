<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

// Public routes (no auth required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Routes for all authenticated users
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    
    // Routes only for admin users
    Route::post('/products', [ProductController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('auth:sanctum');
});