<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'status',
        'type',
        'subtotal',
        'tax',
        'total',
        'customer_id',
        'table_id',
        'user_id',
        'notes'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
