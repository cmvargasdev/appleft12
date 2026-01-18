<?php
use function Livewire\Volt\{state, computed, on};
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Storage;

state([
    'selectedCategory' => null,
    'selectedProduct' => null,
    'showProductModal' => false,
    'loadingProduct' => false, // Nuevo estado para loading
]);

$categories = computed(function () {
    return ProductCategory::select('id', 'name', 'image')
        ->whereHas('products', function ($query) {
            $query->where('status', 1);
        })
        ->orderBy('pos')
        ->get();
});

$products = computed(function () {
    if (!$this->selectedCategory) {
        return collect();
    }

    return Product::select('id', 'name', 'price', 'descrip', 'detail', 'image', 'has_variants')
        ->with(['variants' => function($query) {
            $query->select('id', 'product_id', 'name', 'price');
        }])
        ->where('product_category_id', $this->selectedCategory)
        ->where('status', 1)
        ->orderBy('pos')
        ->get();
});


$jsonproducts = computed(function () {

    return Product::select('id', 'name', 'price', 'descrip', 'detail', 'image', 'has_variants','product_category_id')
        ->with(['variants' => function($query) {
            $query->select('id', 'product_id', 'name', 'price');
        }])
        ->where('status', 1)
        ->orderBy('pos')
        ->get();
});

$selectCategory = function ($categoryId) {
    $this->selectedCategory = $categoryId;
};

$showProductDetails = function ($productId) {
    $this->showProductModal = true;
    $this->loadingProduct = true;
    $this->selectedProduct = null;

    // Simulamos un pequeño delay para mostrar el loading (opcional)
    $this->dispatch('product-loading-started');

    // Cargamos el producto
    $this->selectedProduct = Product::with(['variants', 'category'])
        ->find($productId);

    $this->loadingProduct = false;
};

$closeProductModal = function () {
    $this->showProductModal = false;
    $this->selectedProduct = null;
    $this->loadingProduct = false;
};

$backToCategories = function () {
    $this->selectedCategory = null;
};

// Función helper para obtener el nombre de la categoría seleccionada
$getSelectedCategoryName = function () {
    if (!$this->selectedCategory) {
        return 'Categoría';
    }

    $category = $this->categories->firstWhere('id', $this->selectedCategory);
    return $category ? $category->name : 'Categoría';
};
?>
<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Productos</flux:heading>
        <flux:separator variant="subtle" />
    </div>
    <div class="w-full py-8 min-h-screen">
        <div class="container mx-auto px-4">
            @if(!$selectedCategory && 22==33)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($this->categories as $category)
                    <div
                        class="border-2 rounded shadow-md cursor-pointer overflow-hidden border-neutral-200/70 dark:border-neutral-600 peer-checked:border-yellow-500 peer-checked:bg-yellow-700 dark:peer-checked:bg-neutral-800 dark:peer-checked:border-yellow-400"
                        wire:click="selectCategory({{ $category->id }})"
                    >
                        @if($category->image)
                            <div class="h-40 bg-gray-200 overflow-hidden">
                                <img
                                    src="{{ Storage::url($category->image) }}"
                                    alt="{{ $category->name }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        @else
                            <div class="h-40 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white text-4xl">🍕</span>
                            </div>
                        @endif
                        <div class="p-4 text-center">
                            <h3 class="text-lg font-semibold  mb-2">{{ $category->name }}</h3>
                            <p class="text-sm ">
                                {{ $category->products()->where('status', 1)->count() }} productos
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class=" text-6xl mb-4">📦</div>
                        <p class="text-lg">No hay categorías disponibles</p>
                    </div>
                @endforelse
            </div>
            @endif

            <!-- Vista de Productos por Categoría -->
            @if($selectedCategory && 22==33)
            <div class="rounded-lg shadow-lg">
                <!-- Header de categoría -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-700  p-6 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button
                                wire:click="backToCategories"
                                class="flex items-center space-x-2  hover:text-blue-200 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                <span>Volver a categorías</span>
                            </button>
                        </div>
                        <h2 class="text-2xl font-bold">
                            {{ $this->getSelectedCategoryName() }}
                        </h2>
                        <div class="text-sm">
                            {{ $this->products->count() }} productos
                        </div>
                    </div>
                </div>

                <!-- Lista de productos -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($this->products as $product)
                            <div
                                class="border border-neutral-200/70 dark:border-neutral-600 bg-neutral-200 dark:bg-neutral-700  rounded-lg hover:shadow-md transition-shadow duration-300 cursor-pointer"
                                wire:click="showProductDetails({{ $product->id }})"
                            >

                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>

                                    @if($product->descrip)
                                        <p class="text-sm  mb-3 line-clamp-2">{{ $product->descrip }}</p>
                                    @endif

                                    <div class="flex justify-between items-center">
                                        <div class="text-lg">
                                            @if($product->has_variants)
                                                <span class="text-sm ">Desde</span>
                                                ${{ number_format($product->variants->min('price') ?? $product->price, 2) }}
                                            @else
                                                ${{ number_format($product->price, 2) }}
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium  ">
                                            Ver detalles
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <div class="text-gray-400 text-6xl mb-4">😔</div>
                                <p class="text-gray-500 text-lg mb-2">No hay productos en esta categoría</p>
                                <button
                                    wire:click="backToCategories"
                                    class="text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    Volver a categorías
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif



    <div x-data="{ busqueda: '', categoriaSeleccionada: '' }">
        <!-- Flux Select de categorías -->
    <div class="grid grid-cols-2 gap-4 mb-4">
        <flux:select x-model="categoriaSeleccionada" placeholder="Todas las categorías">
            <flux:select.option value="">Todas las categorías</flux:select.option>
            @foreach($this->categories as $category)
                <flux:select.option value="{{ $category['id'] }}">{{ $category['name'] }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input
            type="text"
            x-model="busqueda"
            placeholder="Buscar..."
        />
    </div>

        <!-- Lista de productos con doble filtro -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->jsonproducts as $product)
                <div x-show="('{{ strtolower($product['name']) }}'.includes(busqueda.toLowerCase()) || busqueda === '') &&
                        (categoriaSeleccionada === '' || '{{ $product['product_category_id'] }}' === categoriaSeleccionada)"
                    class="border border-neutral-200/70 dark:border-neutral-600 bg-neutral-200 dark:bg-neutral-700  rounded-lg hover:shadow-md transition-shadow duration-300 cursor-pointer"
                    wire:click="showProductDetails({{ $product->id }})"
                >

                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>

                        @if($product->descrip)
                            <p class="text-sm  mb-3 line-clamp-2">{{ $product->descrip }}</p>
                        @endif

                        <div class="flex justify-between items-center">
                            <div class="text-lg">
                                @if($product->has_variants)
                                    <span class="text-sm ">Desde</span>
                                    ${{ number_format($product->variants->min('price') ?? $product->price, 2) }}
                                @else
                                    ${{ number_format($product->price, 2) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">😔</div>
                    <p class="text-gray-500 text-lg mb-2">No hay productos en esta categoría</p>
                    <button
                        wire:click="backToCategories"
                        class="text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Volver a categorías
                    </button>
                </div>
            @endforelse
        </div>

    </div>
            <!-- Modal de Detalles del Producto usando Flux -->
            @if($showProductModal)
            <flux:modal name="product-details" class="max-w-2xl" wire:model="showProductModal">
                <div class="space-y-6">
                    @if($loadingProduct || !$selectedProduct)
                        <!-- Estado de carga -->
                        <div class="flex flex-col items-center justify-center py-12">
                            <flux:icon.loading class="w-12 h-12 text-blue-600 mb-4" />
                            <flux:heading size="md">Cargando producto...</flux:heading>
                            <flux:text class="text-gray-500 mt-2">Por favor espere</flux:text>
                        </div>
                    @else
                        <!-- Contenido del producto cuando ya cargó -->
                        <!-- Encabezado -->
                        <div>
                            <flux:heading size="lg">{{ $selectedProduct->name }}</flux:heading>

                            @if($selectedProduct->category)
                                <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm mt-2">
                                    {{ $selectedProduct->category->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Descripción -->
                        @if($selectedProduct->descrip)
                            <flux:text class="text-gray-600">{{ $selectedProduct->descrip }}</flux:text>
                        @endif

                        <!-- Detalles -->
                        @if($selectedProduct->detail)
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {!! nl2br(e($selectedProduct->detail)) !!}
                            </div>
                        @endif

                        <!-- Precios y Variantes -->
                        <div class="border-t border-gray-200 pt-4">
                            <flux:heading size="md">Precios</flux:heading>

                            @if($selectedProduct->has_variants && $selectedProduct->variants->count() > 0)
                                <div class="space-y-2 mt-3">
                                    @foreach($selectedProduct->variants as $variant)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                            <span class="text-gray-700">{{ $variant->name }}</span>
                                            <span class="font-semibold text-blue-600">${{ number_format($variant->price, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex justify-between items-center py-2 mt-3">
                                    <span class="text-gray-700">Precio unitario</span>
                                    <span class="text-2xl font-bold text-blue-600">${{ number_format($selectedProduct->price, 2) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex border-t border-gray-200 pt-4">
                            <flux:spacer />
                            <flux:button
                                wire:click="closeProductModal"
                                class="mr-2"
                            >
                                Cerrar
                            </flux:button>
                            <flux:button variant="primary">
                                Agregar al pedido
                            </flux:button>
                        </div>
                    @endif
                </div>
            </flux:modal>
            @endif
        </div>
    </div>

</section>
