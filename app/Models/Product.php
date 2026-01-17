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
        'pricebs',
        'product_category_id',
        'is_menu_digital',
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

    public function scopeMenuDigital($query)
    {
        return $query->whereHas('category', function ($categoryQuery) {
            $categoryQuery->where('is_menu_digital', true);
        })->where('is_menu_digital', true);
    }
}
