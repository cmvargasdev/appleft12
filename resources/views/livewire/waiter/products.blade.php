<?php
use function Livewire\Volt\{state, mount};
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;
use App\Models\OrderItem;

state([
    'order_id' => 0,
    'order' => null,
    'order_number' => '',

    'categories' => ProductCategory::select('id', 'name', 'image')
        ->whereHas('products', function ($query) {
            $query->where('status', 1);
        })
        ->orderBy('pos')
        ->get()->toArray(),

    'products' => Product::select('id', 'name', 'price', 'descrip', 'detail', 'image', 'has_variants', 'product_category_id')
        ->with(['variants' => function($query) {
            $query->select('id', 'product_id', 'name', 'price');
        }])
        ->where('status', 1)
        ->orderBy('pos')
        ->get()
        ->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'descrip' => $product->descrip,
                'detail' => $product->detail,
                'image' => $product->image,
                'has_variants' => $product->has_variants,
                'product_category_id' => $product->product_category_id,
                'variants' => $product->variants->toArray(),
                'category_name' => $product->category->name ?? null
            ];
        })
        ->toArray(),
]);

mount(function ($order = null) {
    $this->order_id = $order ? (int) $order : 0;

    if ($this->order_id > 0) {
        $this->order = Order::find($this->order_id);
        if ($this->order) {
            $this->order_number = $this->order->order_number ?? '';
        }
    }

    // Generar número de orden automático si no existe
    if (empty($this->order_number)) {
        $this->order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
});

$confirmOrder = function ($items) {
    try {
        // Validar que haya items
        if (empty($items)) {
            session()->flash('error', 'No hay productos en el pedido');
            return;
        }

        // Calcular totales
        $subtotal = collect($items)->sum('total_price');
        $tax = $subtotal * 0.10; // 10% de impuesto, ajusta según necesites
        $total = $subtotal + $tax;

        // Crear o actualizar orden
        if ($this->order_id > 0 && $this->order) {
            // Actualizar orden existente
            $this->order->update([
                'order_number' => $this->order_number,
                'subtotal' => $this->order->subtotal + $subtotal,
                'tax' => $this->order->tax + $tax,
                'total' => $this->order->total + $total,
            ]);
            $order = $this->order;
        } else {
            // Crear nueva orden
            $order = Order::create([
                'order_number' => $this->order_number,
                'status' => 'pendiente',
                'type' => 'dine_in', // Ajusta según necesites
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'customer_id' => null,
                'table_id' => null,
                'user_id' => auth()->id(),
                'notes' => null,
            ]);
        }

        // Agregar items a la orden
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'special_instructions' => $item['special_instructions'] ?? null,
                'status' => 'pending',
            ]);
        }

        session()->flash('success', 'Pedido confirmado exitosamente');

        // Dispatch evento para limpiar el storage en el frontend
        $this->dispatch('order-confirmed', orderId: $order->id);

        // Redirigir a la misma orden para seguir agregando productos
        return redirect()->route('products.order', ['order' => $order->id]);

    } catch (\Exception $e) {
        session()->flash('error', 'Error al confirmar el pedido: ' . $e->getMessage());
    }
};

?>

<section class="w-full" x-data="productStore()">
    <div class="relative w-full">
        <flux:heading size="xl" level="1" class="mb-6">Productos</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Mensajes flash -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="w-full py-8 min-h-screen">
        <div class="container mx-auto px-4">

            <!-- Botón para ver pedido -->
            <div class="mb-4 flex justify-end">
                <flux:modal.trigger name="order-preview">
                    <flux:button variant="primary">
                        <flux:icon.shopping-cart class="mr-2" />
                        Ver Pedido (<span x-text="orderItems.length"></span>)
                    </flux:button>
                </flux:modal.trigger>
            </div>

            <!-- Filtros -->
            <div x-data="{ busqueda: '', categoriaSeleccionada: '' }">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <flux:select x-model="categoriaSeleccionada" placeholder="Todas las categorías">
                        <flux:select.option value="">Todas las categorías</flux:select.option>
                        @foreach($this->categories as $category)
                            <flux:select.option value="{{ $category['id'] }}">
                                {{ $category['name'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input
                        type="text"
                        x-model="busqueda"
                        placeholder="Buscar..."
                    />
                </div>

                <!-- Lista de productos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($this->products as $product)
                        <div x-show="('{{ strtolower($product['name']) }}'.includes(busqueda.toLowerCase()) || busqueda === '') &&
                                (categoriaSeleccionada === '' || '{{ $product['product_category_id'] }}' === categoriaSeleccionada)"
                            class="border border-neutral-200/70 dark:border-neutral-600 bg-neutral-200 dark:bg-neutral-700 rounded-lg hover:shadow-md transition-shadow duration-300 cursor-pointer"
                            x-on:click="openProductModal({{ $product['id'] }})"
                        >
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-2">{{ $product['name'] }}</h3>

                                @if($product['descrip'])
                                    <p class="text-sm mb-3 line-clamp-2">{{ $product['descrip'] }}</p>
                                @endif

                                <div class="flex justify-between items-center">
                                    <div class="text-lg">
                                        @if($product['has_variants'])
                                            <span class="text-sm">Desde</span>
                                            ${{ number_format(min(array_column($product['variants'], 'price') ?? [$product['price']]), 2) }}
                                        @else
                                            ${{ number_format($product['price'], 2) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">😔</div>
                            <p class="text-gray-500 text-lg mb-2">No hay productos disponibles</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal de Detalles del Producto -->
            <flux:modal name="product-details" class="max-w-2xl">
                <div class="space-y-4" x-data="productModal()">
                        <div>
                            <!-- Encabezado -->
                            <div>
                                <flux:heading size="lg" x-text="productData?.name || 'name'"></flux:heading>
                                <template x-if="productData?.category_name">
                                    <flux:text variant="subtle" x-text="productData?.category_name || 'category_name'"></flux:text>
                                </template>
                            </div>

                            <!-- Descripción -->
                            <template x-if="productData?.descrip">
                                <flux:text class="text-gray-600" x-text="productData?.descrip || 'descrip'"></flux:text>
                            </template>

                            <!-- Detalles -->
                            <template x-if="productData?.detail">
                                <div class="prose prose-sm max-w-none text-gray-600"
                                     x-html="productData.detail.replace(/\n/g, '<br>')"></div>
                            </template>

                            <!-- Precios y Cantidad -->
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center py-2 mt-3">
                                    <span class="text-gray-700">Precio unitario</span>
                                    <span class="text-2xl font-bold text-blue-600"
                                          x-text="'$' + unitPrice.toFixed(2)"></span>
                                </div>

                                <flux:input.group class="my-4">
                                    <flux:button
                                        icon="minus"
                                        x-on:click="if(quantity > 1) quantity--"
                                        x-bind:disabled="quantity <= 1"
                                    />
                                    <flux:input
                                        placeholder="Cantidad"
                                        readonly
                                        x-model="quantity"
                                        class="text-center"
                                    />
                                    <flux:button
                                        icon="plus"
                                        x-on:click="quantity++"
                                    />
                                </flux:input.group>

                                <!-- Total calculado -->
                                <div class="flex justify-between items-center py-2 bg-blue-50 dark:bg-blue-900/20 px-4 rounded">
                                    <span class="font-semibold">Total:</span>
                                    <span class="text-2xl font-bold text-blue-600"
                                          x-text="'$' + totalPrice.toFixed(2)"></span>
                                </div>

                                <!-- Instrucciones especiales -->
                                <div class="mt-4">
                                    <flux:label>Instrucciones especiales (opcional)</flux:label>
                                    <flux:textarea
                                        x-model="specialInstructions"
                                        placeholder="Ej: Sin cebolla, extra queso, etc."
                                        rows="3"
                                    />
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex border-t border-gray-200 pt-4 mt-4">
                                    <flux:spacer />
                                    <flux:button
                                        x-on:click="$flux.modal('product-details').close()"
                                        class="mr-2"
                                    >
                                        Cerrar
                                    </flux:button>
                                    <flux:button
                                        variant="primary"
                                        x-on:click="addToOrder()"
                                    >
                                        <flux:icon.plus class="mr-2" />
                                        Agregar al pedido
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                </div>
            </flux:modal>

            <!-- Modal de Pedido Preseleccionado -->
            <flux:modal name="order-preview" class="md:w-[600px]">
                <form wire:submit="confirmOrder(JSON.parse($refs.itemsData.value))" x-data="orderPreview()">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Productos Seleccionados</flux:heading>
                            <flux:text variant="subtle">Revisa tu pedido antes de confirmar</flux:text>
                        </div>

                        <!-- Campo para número de orden -->
                        <div>
                            <flux:label>Número de Orden</flux:label>
                            <flux:input
                                wire:model="order_number"
                                type="text"
                                placeholder="Ej: ORD-20250125-0001"
                                required
                            />
                            <flux:text variant="subtle" class="text-xs mt-1">
                                @if($this->order_id > 0)
                                    Editando orden existente #{{ $this->order_id }}
                                @else
                                    Se creará una nueva orden
                                @endif
                            </flux:text>
                        </div>

                        <!-- Lista de productos en el pedido -->
                        <div class="space-y-4 max-h-[400px] overflow-y-auto">
                            <template x-if="orderItems.length === 0">
                                <div class="text-center py-8">
                                    <flux:icon.shopping-cart class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                                    <flux:text variant="subtle">No hay productos en el pedido</flux:text>
                                </div>
                            </template>

                            <template x-for="(item, index) in orderItems" :key="index">
                                <div class="border border-neutral-200 dark:border-neutral-600 rounded-lg p-4 bg-neutral-50 dark:bg-neutral-800">
                                    <!-- Nombre del producto -->
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <flux:heading size="sm" x-text="item.product_name"></flux:heading>
                                            <flux:text variant="subtle" class="text-xs" x-text="'Precio unitario: $' + parseFloat(item.unit_price).toFixed(2)"></flux:text>
                                        </div>
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            type="button"
                                            @click="removeItem(index)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <flux:icon.trash class="w-4 h-4" />
                                        </flux:button>
                                    </div>

                                    <!-- Cantidad y Total -->
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Cantidad:</span>
                                            <span class="font-semibold" x-text="item.quantity"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Total: </span>
                                            <span class="font-bold text-lg text-blue-600" x-text="'$' + parseFloat(item.total_price).toFixed(2)"></span>
                                        </div>
                                    </div>

                                    <!-- Instrucciones especiales -->
                                    <template x-if="item.special_instructions">
                                        <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800">
                                            <flux:text variant="subtle" class="text-xs" >
                                                <strong>Nota:</strong> <span x-text="item.special_instructions"></span>
                                            </flux:text>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Total del pedido -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center">
                                <flux:heading size="md">Total del Pedido:</flux:heading>
                                <flux:heading size="lg" class="text-blue-600" x-text="'$' + calculateTotal().toFixed(2)"></flux:heading>
                            </div>
                        </div>

                        <!-- Input hidden para pasar los items al backend -->
                        <input type="hidden" x-ref="itemsData" :value="JSON.stringify(orderItems)">

                        <!-- Botones de acción -->
                        <div class="flex gap-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <flux:button type="button" @click="clearOrder()" variant="ghost">
                                Limpiar todo
                            </flux:button>
                            <flux:spacer />
                            <flux:button type="button">
                                Cerrar
                            </flux:button>
                            <flux:button type="submit" variant="primary" x-show="orderItems.length > 0">
                                Confirmar Pedido
                            </flux:button>
                        </div>
                    </div>
                </form>
            </flux:modal>

        </div>
    </div>
</section>

<script>
document.addEventListener('alpine:init', () => {
    // Store para productos y pedido
    Alpine.store('productStore', {
        products: @json($this->products),
        categories: @json($this->categories),
        currentProduct: null,

        getProductById(id) {
            return this.products.find(product => product.id == id);
        },

        filterByCategory(categoryId) {
            if (!categoryId) return this.products;
            return this.products.filter(product => product.product_category_id == categoryId);
        },

        searchByName(searchTerm) {
            if (!searchTerm) return this.products;
            return this.products.filter(product =>
                product.name.toLowerCase().includes(searchTerm.toLowerCase())
            );
        },

        openProductModal(productId) {
            const productData = this.getProductById(productId);
            if (productData) {
                this.currentProduct = productData;
                Flux.modal('product-details').show();
            }
        },

        closeProductModal() {
            this.currentProduct = null;
            Flux.modal('product-details').close();
        }
    });

    // Store para el pedido (actualizado - sin localStorage)
    Alpine.store('order', {
        items: [],

        addToOrder(item) {
            this.items.push(item);
            this.showNotification('Producto agregado al pedido');
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.showNotification('Producto eliminado del pedido');
        },

        clearOrder() {
            this.items = [];
            this.showNotification('Pedido limpiado');
        },

        calculateTotal() {
            return this.items.reduce((total, item) => {
                return total + parseFloat(item.total_price);
            }, 0);
        },

        showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        },

        getItemCount() {
            return this.items.length;
        }
    });

    // Escuchar evento de orden confirmada para limpiar items
    window.addEventListener('order-confirmed', () => {
        Alpine.store('order').clearOrder();
    });
});

function productStore() {
    return {
        openProductModal(productId) {
            Alpine.store('productStore').openProductModal(productId);
        },

        get orderItems() {
            const orderStore = Alpine.store('order');
            return orderStore ? orderStore.items : [];
        },

        get orderCount() {
            const orderStore = Alpine.store('order');
            return orderStore ? orderStore.getItemCount() : 0;
        },

        removeItem(index) {
            const orderStore = Alpine.store('order');
            if (orderStore) orderStore.removeItem(index);
        },

        clearOrder() {
            const orderStore = Alpine.store('order');
            if (orderStore) orderStore.clearOrder();
        },

        calculateTotal() {
            const orderStore = Alpine.store('order');
            return orderStore ? orderStore.calculateTotal() : 0;
        }
    };
}

function productModal() {
    return {
        quantity: 1,
        specialInstructions: '',

        get productData() {
            return Alpine.store('productStore').currentProduct;
        },

        get loading() {
            return !this.productData;
        },

        init() {
            this.$watch('$flux.modal("product-details").open', (isOpen) => {
                if (!isOpen) {
                    this.resetModal();
                }
            });
        },

        get unitPrice() {
            return this.productData ? parseFloat(this.productData.price) : 0;
        },

        get totalPrice() {
            return this.quantity * this.unitPrice;
        },

        addToOrder() {
            if (!this.productData) return;

            Alpine.store('order').addToOrder({
                product_id: this.productData.id,
                product_name: this.productData.name,
                quantity: this.quantity,
                unit_price: this.unitPrice,
                total_price: this.totalPrice,
                special_instructions: this.specialInstructions,
                status: 'pending'
            });

            Alpine.store('productStore').closeProductModal();
        },

        resetModal() {
            this.quantity = 1;
            this.specialInstructions = '';
        }
    };
}

function orderPreview() {
    return {
        get orderItems() {
            const orderStore = Alpine.store('order');
            return orderStore ? orderStore.items : [];
        },

        removeItem(index) {
            const orderStore = Alpine.store('order');
            if (orderStore) orderStore.removeItem(index);
        },

        clearOrder() {
            const orderStore = Alpine.store('order');
            if (orderStore) orderStore.clearOrder();
        },

        calculateTotal() {
            const orderStore = Alpine.store('order');
            return orderStore ? orderStore.calculateTotal() : 0;
        }
    };
}
</script>
