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

Route::middleware(['web'])->group(function () {
    // Obtener items de una orden
    Route::get('/orders/{order}/items', function (App\Models\Order $order) {
        $items = $order->items()->with('product')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'special_instructions' => $item->special_instructions
            ];
        });

        return response()->json([
            'items' => $items,
            'subtotal' => $order->subtotal,
            'total' => $order->total
        ]);
    });

    // Eliminar un item
    Route::delete('/order-items/{orderItem}', function (App\Models\OrderItem $orderItem) {
        $orderItem->delete();

        // Actualizar totales de la orden
        $order = $orderItem->order;
        $subtotal = $order->items()->sum('total_price');

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $subtotal * 0.16,
            'total' => $subtotal * 1.16
        ]);

        return response()->json(['success' => true]);
    });

    // Guardar orden
    Route::post('/orders/{order}/save', function (App\Models\Order $order) {
        $order->update(['status' => 'confirmed']);
        return response()->json(['success' => true]);
    });

    // Limpiar orden
    Route::delete('/orders/{order}/clear', function (App\Models\Order $order) {
        $order->items()->delete();
        $order->update([
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0
        ]);

        return response()->json(['success' => true]);
    });
});
