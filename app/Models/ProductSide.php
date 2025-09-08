<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSide extends Model
{
    protected $fillable = [
        'pos',
        'name',
        'product_category_id',
    ];
}
