<?php

namespace App\Models;

use App\Traits\HasSortablePosition;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasSortablePosition;

    protected $fillable = [
        'pos',
        'name',
        'descrip',
        'detail',  //Slices/Porc Mini:4 Med:6 Fam:8 Extra:12
        'price',
        'product_category_id',
        'has_variants',
        'status',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function availableExtras() {
        return $this->belongsToMany(ProductExtra::class, 'product_extra_availability')->withPivot('price');
    }
}
