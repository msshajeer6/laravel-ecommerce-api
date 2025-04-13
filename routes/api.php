<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('throttle:60,1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        // Public product routes
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);

        // Order routes
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);

        // Admin routes: product/category management
        Route::middleware('role:admin')->group(function () {
            Route::post('products', [ProductController::class, 'store']);
            Route::put('products/{id}', [ProductController::class, 'update']);
            Route::delete('products/{product}', [ProductController::class, 'destroy']);

            Route::apiResource('categories', CategoryController::class);
        });
    });
});
