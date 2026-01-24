<?php

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('throttle:30,1')->get('/', function () {
    return auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login');
})->name('home');


Route::middleware(['auth'])->group(function () {
    //Volt::mount(['waiter' => resource_path('views/livewire/waiter')]);


    Volt::route('/dashboard', 'waiter/tables')->name('dashboard');
    #Volt::route('tables/{table}', 'tables.show')->name('tables.show');
    Volt::route('products', 'waiter/products')->name('products.list');

    #Volt::route('tables', 'tables.index')->name('tables.index');



    Volt::route('orders', 'waiter/orders')->name('orders.list');
    #Volt::route('orders/{order}', 'orders.show')->name('orders.show');

});

Route::middleware(['auth'])->prefix('admin')->group(function () {

    Volt::mount([
        'admin' => base_path('resources/views/livewire/admin')
    ]);

    Route::view('dashboard', 'dashboard')->name('admin.dashboard');
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

    $categories = ProductCategory::orderBy('pos')->get();
     $products = Product::orderBy('pos')->get();
    return view('menu',compact('categories','products'));
})->name('menu');
