<?php

use function Livewire\Volt\{state, mount};
use App\Models\Order;

state([
    'orders' => [],
    'successMessage' => '',
    'order_selected' => null
]);

mount(function () {
    $this->loadOrders();
});

$loadOrders = function () {
    $this->orders = Order::orderBy('id','desc')->limit(50)->get();
};

$selectOrder = function ($orderId) {
    $this->order_selected = $orderId;
};
?>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Ordenes</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <div class="w-full gap-4">
        @foreach ($this->orders as $order)
            <div  class="grid grid-cols-8 gap-4 border-b cursor-pointer">
                <div class="p-2 col-span-2 flex"><flux:badge size="sm" class="mr-2">{{ $order->id }}</flux:badge> {{ $order->order_number }}</div>
                <div class="p-2"></div>
                <div class="p-2">
                    <flux:badge
                        size="sm"
                        color="{{ match(strtolower($order->status)) {
                            'pendiente' => 'yellow',
                            'confirmado' => 'blue',
                            'preparando' => 'indigo',
                            'listo' => 'cyan',
                            'completado' => 'green',
                            'cancelado' => 'red',
                            default => 'gray'
                        } }}"
                        inset="top bottom"
                    >
                        {{ $order->status }}
                    </flux:badge>
                </div>
                <div class="p-2">{{ $order->created_at->format("H:i d/m") }}</div>
                <div class="p-2">{{ $order->total }}</div>
                <div class="p-2 flex">
                    <a class="btn" href="{{ route('products.order', $order) }}">Agregar Productos</a>
                </div>
                <div class="p-2 flex">
                    <a class="btn" href="{{ route('orders.items', $order) }}">Ver Orden</a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Mostrar la mesa seleccionada -->
    @if($order_selected)
        <div class="mt-6 p-4 border border-blue-200 rounded-lg">
            <flux:heading size="lg" level="2">Orden seleccionada:</flux:heading>
            <p class="font-semibold">
                @php
                    $selectedOrder = $orders->firstWhere('id', $order_selected);
                @endphp
                @if($selectedOrder)
                    Orden {{ $selectedOrder->id }}
                @endif
            </p>
        </div>
    @else
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-yellow-700">Selecciona una Orden</p>
        </div>
    @endif
</section>
