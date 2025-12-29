<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Product API routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
});

// Cart API routes (requires authentication)
Route::prefix('cart')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::get('/count', [CartController::class, 'count']);
    Route::post('/', [CartController::class, 'store']);
    Route::put('/{id}', [CartController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/{id}', [CartController::class, 'destroy'])->where('id', '[0-9]+');
    Route::delete('/', [CartController::class, 'clear']);
});

// Order API routes (requires authentication)
Route::prefix('orders')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show'])->where('id', '[0-9]+');
});
