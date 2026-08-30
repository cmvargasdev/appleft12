<?php

use Livewire\Volt\Volt;

Volt::route('/', 'admin.product-categories.index')->name('index');
Volt::route('/create', 'admin.product-categories.form')->name('create');
Volt::route('/{product_category}/edit', 'admin.product-categories.form')->name('edit');
//Volt::route('/{product}', 'admin.product-categories.show')->name('show');
