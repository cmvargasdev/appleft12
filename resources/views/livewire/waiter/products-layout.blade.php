<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="flex min-h-screen">

    <!-- Contenido Products -->
    <div class="flex-1 ">
        <livewire:waiter.products />
    </div>
    <!-- Sidebar OrderPreview -->
    <div class="w-96 border-l border-gray-200">
        <livewire:waiter.order-preview />
    </div>
</div>
