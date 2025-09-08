<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">

    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Productos</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <x-products.layout :heading="'Productos'" >
        <div>
            //extras
        </div>
    </x-products.layout>
</section>
