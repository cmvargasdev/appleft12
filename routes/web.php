<?php

use App\Http\Controllers\Admin\ProductController;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('throttle:30,1')->get('/', function () {
    return auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login');
})->name('home');


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::redirect('/', 'admin/dashboard');

    Route::prefix('products')
            ->name('products.')
            ->group(base_path('routes/web/products.php'));

    Route::prefix('product-categories')
            ->name('product-categories.')
            ->group(base_path('routes/web/product-categories.php'));

    Route::prefix('product-extras')
            ->name('product-extras.')
            ->group(base_path('routes/web/product-extras.php'));

    Route::prefix('product-sides')
            ->name('product-sides.')
            ->group(base_path('routes/web/product-sides.php'));







    // Volt::route('products', 'products.index')->name('products.index');
    // Volt::route('products/create', 'products.create')->name('products.create');
    // Volt::route('products/{product}/edit', 'products.edit')->name('products.edit');
    // Volt::route('products/{product}', 'products.show')->name('products.show');
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});
require __DIR__.'/auth.php';
Route::get('menu', function () {

    $categories = ProductCategory::select('id', 'name','pos')
        ->orderBy('pos')
        ->get();
     $products = Product::select('id', 'name','pos','product_category_id')
            ->orderBy('pos')
            ->get();

        foreach( $categories as $cat){
            foreach( $products->where('product_category_id',$cat->id) as $prod){
                echo "<br> c:$prod->product_category_id - pos:$prod->pos / $prod->name";
            }
        }
})->name('menu');
