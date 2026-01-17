<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function products()
    {
        $products = Product::select('id', 'name', 'pos', 'descrip', 'detail','product_category_id', 'has_variants','price')
                ->with('variants')
                ->orderBy('pos')
                ->get();
        return response()->json($products);
    }



    public function categories()
    {
        $categories = ProductCategory::select('id', 'name','pos','image')
                ->orderBy('pos')
                ->get();
        return response()->json($categories);
    }
}
