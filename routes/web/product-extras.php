<?php

use Livewire\Volt\Volt;

Volt::route('/', 'product-extras.index')->name('index');
Volt::route('/create', 'product-extras.create')->name('create');
Volt::route('/{product}/edit', 'product-extras.edit')->name('edit');
Volt::route('/{product}', 'product-extras.show')->name('show');
