<?php

use Livewire\Volt\Component;
use App\Models\Order;
use function Livewire\Volt\{state, rules};

new class extends Component {
    public Order $order;

    // Estados para los campos editables
    public string $order_number;
    public string $status;
    public bool $isSaving = false;

    // Reglas de validación
    public function rules(): array
    {
        return [
            'order_number' => 'required|string|max:255',
            'status' => 'required|in:pendiente,confirmado,preparando,listo,completado,cancelado',
        ];
    }

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->order_number = $order->order_number;
        $this->status = $order->status;
    }

    // Método para guardar cambios
    public function save(): void
    {
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        try {
            // Validar los datos
            $validated = $this->validate();

            // Actualizar el pedido
            $this->order->update($validated);

            // Mostrar mensaje de éxito
            session()->flash('success', 'Pedido actualizado correctamente');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el pedido: ' . $e->getMessage());
        } finally {
            $this->isSaving = false;
        }
    }

    public function with(): array
    {
        return [
            'items' => $this->order->items()->get(),
        ];
    }
}; ?>

<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Items del Pedido #{{ $order->id }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Formulario para editar order_number y status -->
    <form wire:submit.prevent="save" class="mb-6">
        <div class="flex gap-4 mb-6">
            <flux:field class="flex-1">
                <flux:input
                    wire:model="order_number"
                    label="Número de Pedido"
                    placeholder="Ingrese el número de pedido"
                />
                @error('order_number')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </flux:field>

            <flux:field class="flex-1">
                <flux:select
                    wire:model="status"
                    label="Estado del Pedido"
                >
                    <flux:select.option value="pendiente">Pendiente</flux:select.option>
                    <flux:select.option value="confirmado">Confirmado</flux:select.option>
                    <flux:select.option value="preparando">Preparando</flux:select.option>
                    <flux:select.option value="listo">Listo</flux:select.option>
                    <flux:select.option value="completado">Completado</flux:select.option>
                    <flux:select.option value="cancelado">Cancelado</flux:select.option>
                </flux:select>
                @error('status')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </flux:field>

            <flux:field>
                <flux:label></flux:label>
                <flux:button
                    variant="primary"
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove>
                        Guardar
                    </span>
                    <span wire:loading>
                        Guardando...
                    </span>
                </flux:button>
            </flux:field>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session()->has('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif
    </form>

    <!-- Lista de items -->
    <div class="space-y-4">
        @foreach($items as $item)
            <div class="border border-neutral-200 dark:border-neutral-600 rounded-lg p-4 bg-neutral-50 dark:bg-neutral-800">
                <!-- Nombre del producto -->
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <flux:heading size="sm">{{ $item->product->name }}</flux:heading>
                        <flux:text variant="subtle" class="text-xs">
                            Precio unitario: ${{ number_format($item->unit_price, 2) }}
                        </flux:text>
                    </div>
                    <flux:button
                        size="sm"
                        variant="ghost"
                        class="text-red-600 hover:text-red-800"
                        wire:click="deleteItem({{ $item->id }})"
                    >
                        <flux:icon.trash class="w-4 h-4" />
                    </flux:button>
                </div>

                <!-- Cantidad y Total -->
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cantidad:</span>
                        <span class="font-semibold">{{ $item->quantity }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total: </span>
                        <span class="font-bold text-lg text-blue-600">
                            ${{ number_format($item->total_price, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Instrucciones especiales -->
                @if(strlen($item->special_instructions) > 2)
                    <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800">
                        <flux:text variant="subtle" class="text-xs" >
                            <strong>Nota:</strong> <span>{{ $item->special_instructions }}</span>
                        </flux:text>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

<div class="space-y-4">
    <!-- Encabezado de la orden -->
    <div class="bg-neutral-100 dark:bg-neutral-700 rounded-lg p-4 mb-4">
        <div class="flex flex-wrap justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Orden #ORD-001</h2>
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Cliente: Juan Pérez • Mesa: 5</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    Preparando
                </span>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Fecha: 15/01/2024</p>
            </div>
        </div>
    </div>

    <!-- Listado de items de la orden -->
    <div class="space-y-3">
        <!-- Item 1 - Desktop -->
        <div class="border border-neutral-200 dark:border-neutral-600 rounded-lg p-4 bg-neutral-50 dark:bg-neutral-800">
            <!-- Desktop: una línea -->
            <div class="hidden md:flex items-center justify-between">
                <div class="flex-1 grid grid-cols-12 gap-4 items-center">
                    <!-- Producto -->
                    <div class="col-span-4">
                        <div class="flex items-center space-x-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 font-medium">
                                2x
                            </span>
                            <div>
                                <h3 class="font-medium text-neutral-900 dark:text-neutral-100">Pizza Pepperoni Grande</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">Extra queso, Orégano extra</p>
                            </div>
                        </div>
                    </div>

                    <!-- Precio Unitario -->
                    <div class="col-span-2">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Unitario:</span>
                        <p class="text-lg font-medium text-neutral-900 dark:text-neutral-100">$12.50</p>
                    </div>

                    <!-- Precio Total -->
                    <div class="col-span-2">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Total:</span>
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">$25.00</p>
                    </div>

                    <!-- Estado del item -->
                    <div class="col-span-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            Preparando
                        </span>
                    </div>

                    <!-- Acciones -->
                    <div class="col-span-2 flex justify-end space-x-2">
                        <button class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Editar</span>
                        </button>
                        <button class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Eliminar</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile: múltiples líneas -->
            <div class="md:hidden space-y-3">
                <!-- Línea 1: Producto y cantidad -->
                <div class="flex justify-between items-start">
                    <div class="flex items-start space-x-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 font-medium">
                            2x
                        </span>
                        <div>
                            <h3 class="font-medium text-neutral-900 dark:text-neutral-100">Pizza Pepperoni Grande</h3>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Extra queso, Orégano extra</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        Preparando
                    </span>
                </div>

                <!-- Línea 2: Precios -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Precio Unitario:</span>
                        <p class="text-lg font-medium text-neutral-900 dark:text-neutral-100">$12.50</p>
                    </div>
                    <div>
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Total Item:</span>
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">$25.00</p>
                    </div>
                </div>

                <!-- Línea 3: Acciones -->
                <div class="flex justify-end space-x-2 pt-2 border-t border-neutral-200 dark:border-neutral-700">
                    <button class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Editar</span>
                    </button>
                    <button class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Eliminar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Item 2 - Desktop -->
        <div class="border border-neutral-200 dark:border-neutral-600 rounded-lg p-4 bg-neutral-50 dark:bg-neutral-800">
            <div class="hidden md:flex items-center justify-between">
                <div class="flex-1 grid grid-cols-12 gap-4 items-center">
                    <div class="col-span-4">
                        <div class="flex items-center space-x-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 font-medium">
                                1x
                            </span>
                            <div>
                                <h3 class="font-medium text-neutral-900 dark:text-neutral-100">Ensalada César</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">Sin crutones, Aderezo aparte</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Unitario:</span>
                        <p class="text-lg font-medium text-neutral-900 dark:text-neutral-100">$8.75</p>
                    </div>

                    <div class="col-span-2">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Total:</span>
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">$8.75</p>
                    </div>

                    <div class="col-span-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Listo
                        </span>
                    </div>

                    <div class="col-span-2 flex justify-end space-x-2">
                        <button class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Editar</span>
                        </button>
                        <button class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Eliminar</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:hidden space-y-3">
                <div class="flex justify-between items-start">
                    <div class="flex items-start space-x-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 font-medium">
                            1x
                        </span>
                        <div>
                            <h3 class="font-medium text-neutral-900 dark:text-neutral-100">Ensalada César</h3>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Sin crutones, Aderezo aparte</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Listo
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Precio Unitario:</span>
                        <p class="text-lg font-medium text-neutral-900 dark:text-neutral-100">$8.75</p>
                    </div>
                    <div>
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">Total Item:</span>
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">$8.75</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-neutral-200 dark:border-neutral-700">
                    <button class="px-3 py-1.5 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Editar</span>
                    </button>
                    <button class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Eliminar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de montos -->
    <div class="border border-neutral-200 dark:border-neutral-600 rounded-lg p-6 bg-white dark:bg-neutral-900 mt-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
            <!-- Totales -->
            <div class="space-y-2">
                <div class="flex justify-between md:block">
                    <span class="text-neutral-600 dark:text-neutral-400">Subtotal:</span>
                    <span class="text-lg font-medium text-neutral-900 dark:text-neutral-100 md:ml-4">$33.75</span>
                </div>
                <div class="flex justify-between md:block">
                    <span class="text-neutral-600 dark:text-neutral-400">Impuestos (16%):</span>
                    <span class="text-lg font-medium text-neutral-900 dark:text-neutral-100 md:ml-4">$5.40</span>
                </div>
                <div class="flex justify-between md:block border-t border-neutral-200 dark:border-neutral-700 pt-2">
                    <span class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Total:</span>
                    <span class="text-2xl font-bold text-green-600 dark:text-green-400 md:ml-4">$39.15</span>
                </div>
            </div>

            <!-- Acciones de la orden -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <button class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Cancelar Orden</span>
                </button>
                <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition-colors flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Completar Orden</span>
                </button>
            </div>
        </div>
    </div>
</div>
</section>
