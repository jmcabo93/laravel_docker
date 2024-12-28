<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\AuthController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Rutas para el modelo Product
    Route::apiResource('products', ProductController::class);

    // Rutas para el modelo Category
    Route::apiResource('categories', CategoryController::class);

    // Rutas para el modelo Order
    Route::apiResource('orders', OrderController::class);

    // Rutas para el modelo OrderItem
    Route::apiResource('order-items', OrderItemController::class);

    Route::post('logout', [AuthController::class, 'logout']);

     

    Route::get('orders/{id}/cancel', [OrderController::class, 'cancel_order']);
    Route::get('orders/{id}/status', [OrderController::class, 'status_order']);
    Route::get('random_product', [ProductController::class, 'random_product']);

});


