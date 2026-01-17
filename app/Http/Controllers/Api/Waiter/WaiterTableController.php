<?php

namespace App\Http\Controllers\Api\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class WaiterTableController extends Controller
{
    public function getTables()
    {
        $tables = Table::orderBy('cod')->where('is_active',1)->get();
        //return OrderResource::collection($orders);
        return response()->json($tables);
    }
}
