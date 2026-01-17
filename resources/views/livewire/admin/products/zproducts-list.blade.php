<?php
use function Livewire\Volt\{state, mount, on};
use App\Models\Product;

state([
    'products' => [],
    'successMessage' => ''
]);

mount(function () {
    $this->loadProducts();
});

// Escuchar el evento cuando se guarda un producto
on(['productSaved' => function ($message) {
    $this->successMessage = $message;
    $this->loadProducts();
}]);

$loadProducts = function () {
    $this->products = Product::with('category', 'variants')->orderBy('pos')->get();
};

$openCreateModal = function () {
    $this->dispatch('openProductModal');
};

$openEditModal = function ($productId) {
    $this->dispatch('openProductModal', productId: $productId);
};

$deleteProduct = function ($productId) {
    Product::find($productId)?->delete();
    $this->loadProducts();
    $this->successMessage = 'Producto eliminado correctamente';
};

?>

<section class="w-full">
    <!-- Header -->
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-center mb-6">
            <flux:heading size="xl" level="1">Productos</flux:heading>
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                Nuevo Producto
            </flux:button>
        </div>
        <flux:separator variant="subtle" />
    </div>

    <!-- Mensaje de éxito -->
    @if($successMessage)
        <div class="mb-6">
            <div variant="success" class="flex items-center justify-between">
                <div >{{ $successMessage }}</div>
                <flux:button wire:click="$set('successMessage', '')" variant="ghost" size="sm" icon="x" />
            </div>
        </div>
    @endif

    <!-- Tabla de productos con Flux -->
    <table>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Variantes</th>
            <th>Acciones</th>
        </tr>

        <tbody>
            @forelse($products as $product)
                <tr wire:key="product-{{ $product->id }}">

                    <!-- Columna Producto -->
                    <td class="font-medium">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                            @if($product->descrip)
                                <div class="text-sm text-gray-500 mt-1">{{ $product->descrip }}</div>
                            @endif
                        </div>
                    </td>

                    <!-- Columna Categoría -->
                    <td>
                        <flux:badge size="sm" color="zinc">
                            {{ $product->category->name ?? 'Sin categoría' }}
                        </flux:badge>
                    </td>

                    <!-- Columna Precio -->
                    <td>
                        @if($product->has_variants)
                            <flux:text class="text-gray-500 italic">Ver variantes</flux:text>
                        @else
                            <flux:text class="font-semibold">${{ number_format($product->price, 2) }}</flux:text>
                        @endif
                    </td>

                    <!-- Columna Variantes -->
                    <td>
                        @if($product->has_variants && $product->variants->count() > 0)
                            <div class="space-y-1">
                                @foreach($product->variants->take(3) as $variant)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">{{ $variant->name }}:</span>
                                        <span class="font-medium text-gray-900">${{ number_format($variant->price, 2) }}</span>
                                    </div>
                                @endforeach
                                @if($product->variants->count() > 3)
                                    <flux:text class="text-xs text-gray-400">
                                        +{{ $product->variants->count() - 3 }} variante(s) más...
                                    </flux:text>
                                @endif
                            </div>
                        @else
                            <flux:text class="text-gray-400 text-sm">Sin variantes</flux:text>
                        @endif
                    </td>

                    <!-- Columna Acciones -->
                    <td>
                        <div class="flex items-center gap-2">
                            <flux:button
                                wire:click="openEditModal({{ $product->id }})"
                                size="sm"
                                variant="ghost"
                                icon="pencil"
                            >
                                Editar
                            </flux:button>

                            <flux:button
                                wire:click="deleteProduct({{ $product->id }})"
                                wire:confirm="¿Estás seguro de que deseas eliminar '{{ $product->name }}'?"
                                size="sm"
                                variant="danger"
                                icon="trash"
                            >
                                Eliminar
                            </flux:button>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 mb-4 text-gray-300">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <flux:heading size="lg" class="text-gray-900 mb-2">
                                No hay productos registrados
                            </flux:heading>
                            <flux:text class="text-gray-500 mb-6">
                                Comienza creando tu primer producto para gestionar tu inventario
                            </flux:text>
                            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                                Crear primer producto
                            </flux:button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Incluir el componente modal -->
    <livewire:products.modal />
</section>
