<?php

namespace App\Models;

use App\Traits\HasSortablePosition;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasSortablePosition;

     protected $fillable = [
        'pos',
        'name',
        'image',
        //'addons', // antes lo usaba desde categoria para todos
        //'type',
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
