<?php

use Livewire\Volt\Volt;

Volt::route('/', 'product-sides.index')->name('index');
Volt::route('/create', 'product-sides.create')->name('create');
Volt::route('/{product}/edit', 'product-sides.edit')->name('edit');
Volt::route('/{product}', 'product-sides.show')->name('show');
