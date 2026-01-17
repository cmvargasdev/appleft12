<?php

namespace App\Http\Controllers\Api\Waiter;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class WaiterProductController extends Controller
{
    public function getProducts()
    {
        $products = Product::select('id', 'name', 'pos', 'descrip', 'detail','product_category_id', 'has_variants','price')
                ->with('variants')
                ->orderBy('pos')
                ->get();
        return response()->json($products);
    }



    public function getCategories()
    {
        $categories = ProductCategory::select('id', 'name','pos','image')
                ->orderBy('pos')
                ->get();
        return response()->json($categories);
    }
}
