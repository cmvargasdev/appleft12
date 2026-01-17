<?php

namespace App\Http\Controllers\Api\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class WaiterOrderController extends Controller
{
    public function getOrders()
    {
        $orders = Order::all();
        return response()->json($orders);
    }
}
