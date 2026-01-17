<?php

use function Livewire\Volt\{state, mount};
use App\Models\Table;

state([
    'tables' => [],
    'successMessage' => '',
    'table_selected' => null
]);

mount(function () {
    $this->loadTables();
});

$loadTables = function () {
    $this->tables = Table::where('is_active', true)->orderBy('cod')->get();
};

$selectTable = function ($tableId) {
    $this->table_selected = $tableId;
};
?>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Mesas</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($tables as $table)
            <div class="relative">
                <input
                    type="radio"
                    name="table_selected"
                    id="table-{{ $table->id }}"
                    value="{{ $table->id }}"
                    {{ $table_selected == $table->id ? 'checked' : '' }}
                    class="hidden peer"
                >
                <label
                    for="table-{{ $table->id }}"
                    wire:click="selectTable({{ $table->id }})"
                    class="inline-flex items-center justify-between w-full p-5 border-2 rounded cursor-pointer group border-neutral-200/70 dark:border-neutral-600 peer-checked:border-yellow-500 peer-checked:bg-yellow-700 dark:peer-checked:bg-neutral-800 dark:peer-checked:border-yellow-400"                >
                    <div class="flex items-center space-x-5">
                        <flux:icon name="bookmark" class="w-6 h-6" />
                        <div class="flex flex-col justify-start">
                            <div class="w-full text-lg font-semibold">Mesa {{ $table->cod }}</div>
                            <div class="w-full text-sm opacity-60">
                                @if($table->capacity)
                                    Capacidad: {{ $table->capacity }} personas
                                @else
                                    Mesa disponible
                                @endif
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        @endforeach
    </div>

    <!-- Mostrar la mesa seleccionada -->
    @if($table_selected)
        <div class="mt-6 p-4 border border-blue-200 rounded-lg">
            <flux:heading size="lg" level="2">Mesa seleccionada:</flux:heading>
            <p class="font-semibold">
                @php
                    $selectedTable = $tables->firstWhere('id', $table_selected);
                @endphp
                @if($selectedTable)
                    Mesa {{ $selectedTable->cod }}
                @endif
            </p>
        </div>
    @else
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-yellow-700">Por favor selecciona una mesa</p>
        </div>
    @endif
</section>
