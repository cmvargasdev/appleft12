<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'special_instructions',
        #'selected_variants',
        #'selected_extras',
        #'selected_sides',
        'status' // pending, preparing, ready, served, cancelled
    ];

    // Relación con Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relación con Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
