<?php

use Livewire\Volt\Volt;

Volt::route('/', 'product-categories.index')->name('index');
Volt::route('/create', 'product-categories.form')->name('create');
Volt::route('/{product_category}/edit', 'product-categories.form')->name('edit');
//Volt::route('/{product}', 'product-categories.show')->name('show');
