<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\Waiter\WaiterProductController;
use App\Http\Controllers\Api\Waiter\WaiterTableController;
use App\Http\Controllers\Api\WaiterController; // Nuevo controller específico
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas administrativas (existentes)
Route::resource('orders', OrderController::class);
Route::resource('tables', TableController::class);
Route::get('products', [ProductController::class, 'products']);
Route::get('categories', [ProductController::class, 'categories']);

// 🔥 NUEVAS RUTAS ESPECÍFICAS PARA MESEROS
Route::prefix('waiter')->group(function () {
    // Mesas para meseros
    Route::get('tables', [WaiterTableController::class, 'getTables']);
    Route::get('tables/{id}', [WaiterTableController::class, 'getTable']);
    Route::get('tables/{id}/current-order', [WaiterTableController::class, 'getTableCurrentOrder']);
    Route::patch('tables/{id}/status', [WaiterTableController::class, 'updateTableStatus']);

    // Órdenes para meseros
    Route::post('tables/{id}/orders', [WaiterTableController::class, 'createOrder']);
    Route::get('tables/{tableId}/orders/{orderId}', [WaiterTableController::class, 'getOrder']);
    # Route::patch('orders/{id}/status', [WaiterController::class, 'updateOrderStatus']);
    # Route::post('orders/{id}/items', [WaiterController::class, 'addItemToOrder']);
    # Route::delete('orders/{orderId}/items/{itemId}', [WaiterController::class, 'removeItemFromOrder']);

    // Productos para meseros (solo lectura)
    Route::get('products', [WaiterProductController::class, 'getProducts']);
    Route::get('categories', [WaiterProductController::class, 'getCategories']);
});
