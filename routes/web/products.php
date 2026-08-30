<?php

use Livewire\Volt\Volt;

Volt::route('/', 'admin.products.index')->name('index');
Volt::route('/create', 'admin.products.form')->name('create');
Volt::route('/{product}/edit', 'admin.products.form')->name('edit');
//Volt::route('/{product}', 'admin.products.show')->name('show');
