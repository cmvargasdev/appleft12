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
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
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
                    <flux:select.option value="pending">Pendiente</flux:select.option>
                    <flux:select.option value="confirmed">Confirmado</flux:select.option>
                    <flux:select.option value="preparing">En Preparación</flux:select.option>
                    <flux:select.option value="ready">Listo</flux:select.option>
                    <flux:select.option value="completed">Completado</flux:select.option>
                    <flux:select.option value="cancelled">Cancelado</flux:select.option>
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
                            Precio unitario: ${{ number_format($item->price, 2) }}
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
                            ${{ number_format($item->quantity * $item->price, 2) }}
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
</section>
