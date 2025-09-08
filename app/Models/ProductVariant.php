<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{

    protected $fillable = [
        'pos',
        'name',
        'descrip',
        'price',
        'product_id',
    ];
}
