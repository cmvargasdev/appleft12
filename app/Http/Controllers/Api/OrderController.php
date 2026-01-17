<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::paginate(10);
        //return OrderResource::collection($orders);
        return response()->json($orders);

    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:dine_in,takeaway,delivery',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'table_id' => 'nullable|integer|exists:tables,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string|max:255',
            'items.*.selected_variants' => 'nullable|array',
            'items.*.selected_extras' => 'nullable|array',
            'items.*.selected_sides' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Calcular precios en el backend
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $product->price;

                // Calcular extras/adicionale si existen
                $extrasPrice = 0;
                if (!empty($item['selected_extras'])) {
                    // Aquí buscarías los precios de los extras en la base de datos
                    $extras = ProductExtra::whereIn('id', $item['selected_extras'])->get();
                    $extrasPrice = $extras->sum('price');
                }

                $finalUnitPrice = $unitPrice + $extrasPrice;
                $itemTotal = $finalUnitPrice * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $finalUnitPrice,
                    'total_price' => $itemTotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                    'selected_variants' => $item['selected_variants'] ?? null,
                    'selected_extras' => $item['selected_extras'] ?? null,
                    'selected_sides' => $item['selected_sides'] ?? null,
                ];
            }

            // Calcular impuestos (ejemplo: 10%)
            $taxRate = 0.10;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            // Crear la orden
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'status' => 'pending',
                'type' => $validated['type']??'dine_in',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'user_id' => 1,//$validated['user_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Crear items
            $order->items()->createMany($orderItemsData);

            DB::commit();

            $order->load('items.product');

            return response()->json($order, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error creating order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'sometimes|string|max:255',
            'total' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:pending,paid,shipped,cancelled',
        ]);

        $order->update($validated);

        //return new OrderResource($order);
        return response()->json($order, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully.'
        ], 200);
    }
}
