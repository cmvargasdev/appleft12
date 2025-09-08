<?php

use Livewire\Volt\Volt;

Volt::route('/', 'products.index')->name('index');
Volt::route('/create', 'products.form')->name('create');
Volt::route('/{product}/edit', 'products.form')->name('edit');
//Volt::route('/{product}', 'products.show')->name('show');
