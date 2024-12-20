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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\AuthController;

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

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('cancel_order/{id}', [OrderController::class, 'cancel_order']);
    Route::get('status_order/{id}', [OrderController::class, 'status_order']);
    Route::get('random_product', [ProductController::class, 'random_product']);

});

