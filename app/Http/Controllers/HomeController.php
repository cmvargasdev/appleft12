<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $tables = Table::where('is_active', true)->orderBy('cod')->get();
        //return OrderResource::collection($orders);
        return response()->json($tables);
    }
}
