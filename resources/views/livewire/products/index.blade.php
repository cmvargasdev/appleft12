<?php
use function Livewire\Volt\{uses, with, state, computed, mount,updated,on};
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProductCategory; // Asumiendo que tienes un modelo Category

uses(WithPagination::class);

state([
    'move_id' => null,
    'search' => '',
    'product_category_id' => '',
]);

with(fn () => [
    'products' => Product::select('id', 'name', 'pos', 'product_category_id','has_variants')
        ->with('category:id,name') // Eager loading de la categoría
        ->when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->when($this->product_category_id, function ($query) {
            $query->where('product_category_id', $this->product_category_id );
        })
        ->orderBy('pos')
        ->paginate(25)
        ->withQueryString(), // Para mantener los filtros en la paginación

    'categories' => ProductCategory::select('id', 'name')
        ->orderBy('pos')
        ->get(),
]);
$loadProducts = function () {
    $this->products = Product::select('id', 'name', 'pos', 'product_category_id')
        ->with('category:id,name') // Eager loading de la categoría
        ->when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->when($this->product_category_id, function ($query) {
            $query->where('product_category_id', $this->product_category_id );
        })
        ->orderBy('pos')
        ->paginate(25)
        ->withQueryString();
};

on(['productSaved' => function ($message) {
    $this->successMessage = $message;
    $this->loadProducts();
}]);
mount(function () {
    $this->search = session('filters.search', '');
    $this->product_category_id = session('filters.product_category_id', null);
});

$openCreateModal = function () {
    $this->dispatch('openProductModal');
};

$openEditModal = function ($productId) {
    $this->dispatch('openProductModal', productId: $productId);
};

$moveUp = function ($id) {
    $this->move_id = $id;
    $item = Product::findOrFail($id);
    $item->moveUp($item->product_category_id);
};

$moveDown = function ($id) {
    $this->move_id = $id;
    $item = Product::findOrFail($id);
    $item->moveDown($item->product_category_id);
};

$showMove = computed(function () {
    return $this->move_id;
});

// Método para limpiar filtros
$clearFilters = function () {
    $this->search = '';
    $this->product_category_id = '';
    $this->resetPage(); // Reset pagination
};

// Reset pagination cuando cambien los filtros
$updated = function ($property) {
    if (in_array($property, ['search', 'product_category_id'])) {
        $this->resetPage();
        session()->put('filters', [
            'search' => $this->search,
            'product_category_id' => $this->product_category_id,
        ]);
    }
};
?>

<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Productos</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <x-products.layout :heading="'Listado de Productos'">
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <!-- Botón crear producto -->
            <div>
                <flux:button variant="primary" wire:click="openCreateModal">
                    Crear Producto
                </flux:button>
            </div>

            <!-- Filtros -->
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <!-- Input de búsqueda -->
                <div class="flex-1">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar productos..."
                        type="search"
                    />
                </div>

                <!-- Selector de categoría -->
                <div class="min-w-48">
                    <flux:select wire:model.live="product_category_id">
                        <flux:select.option value="">Todas las categorías</flux:option>
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Botón limpiar filtros -->
                @if($search || $product_category_id)
                    <div>
                        <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                            Limpiar filtros
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Indicadores de filtros activos -->
        @if($search || $product_category_id)
            <div class="mb-4 flex flex-wrap gap-2">
                @if($search)
                    <flux:badge variant="outline">
                        Búsqueda: "{{ $search }}"
                        <flux:button
                            wire:click="$set('search', '')"
                            variant="ghost"
                            size="xs"
                            class="ml-1 text-gray-400 hover:text-gray-600"
                        >
                            ×
                        </flux:button>
                    </flux:badge>
                @endif

                @if($product_category_id)
                    @php
                        $selectedCategory = $categories->firstWhere('id', $product_category_id);
                    @endphp
                    @if($selectedCategory)
                        <flux:badge variant="outline">
                            Categoría: {{ $selectedCategory->name }}
                            <flux:button
                                wire:click="$set('product_category_id', '')"
                                variant="ghost"
                                size="xs"
                                class="ml-1 text-gray-400 hover:text-gray-600"
                            >
                                ×
                            </flux:button>
                        </flux:badge>
                    @endif
                @endif
            </div>
        @endif
<flux:modal.trigger name="product-modal">    <flux:button>Edit profile</flux:button></flux:modal.trigger>

        <div class="shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($products as $product)
                    <li class="px-6 py-2">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <flux:link
                                    class="text-sm font-medium"
                                    variant="ghost"
                                >
                                    {{ $product->name }}
                                </flux:link>
                                @if($product->category)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $product->category->name }}
                                    </div>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">
                                    @if($product->has_variants) <b>Var:</b>
                                        @foreach ($product->variants as $variant)
                                            {{ $variant->name }}:{{ $variant->price }} /
                                        @endforeach
                                    @else
                                        <b>Uni:</b> {{$product->price}}
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2 items-center">
                                <flux:button icon="pencil-square" size="xs" wire:click="openEditModal({{ $product->id }})"/>

                                @if($product_category_id)
                                    @if($move_id == $product->id)
                                        <span class="text-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 25" stroke-width="1.5" stroke="currentColor" class="w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                            </svg>
                                        </span>
                                    @endif
                                    <flux:button icon="chevron-up" size="xs" wire:click="moveUp({{ $product->id }})"/>
                                    <flux:button icon="chevron-down" size="xs" wire:click="moveDown({{ $product->id }})"/>
                                    <div class="p-2">{{$product->pos}}</div>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-center">
                        @if($search || $product_category_id)
                            No se encontraron productos que coincidan con los filtros aplicados.
                        @else
                            No hay productos disponibles
                        @endif
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Paginación -->
        <div class="mb-4">
            {{ $products->links() }}
        </div>
            <livewire:products.form-modal />
    </x-products.layout>
</section>
