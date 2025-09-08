<?php
use function Livewire\Volt\{state, rules, mount, computed};
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
// categorias
state([
    'name' => '',
    'descrip' => '',
    //'detail',  //Slices/Porc Mini:4 Med:6 Fam:8 Extra:12
    'price' => '0',
    'product_category_id' => '',
    'has_variants' => 0,

    'image' => null, // Para la nueva imagen
    'currentImage' => null,

    'productId' => null,
    'isSaving' => false,
    'categories' => [],
    'product_variants' => []
]);

//rules(['name' => 'required', 'price' => 'required|numeric']);
rules(['name' => 'required', 'price' => 'required|numeric' , 'product_category_id' => 'required|integer','has_variants'=>'required|numeric']);

mount(function ($product = null) {
    $this->categories = ProductCategory::pluck('name', 'id')->toArray();

    if ($product) {
        $product = Product::findOrFail($product);

         $this->product_variants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'price' => $variant->price,
                'is_existing' => true,
            ];
        })->toArray();

        $this->fill($product->only(['name', 'descrip', 'price', 'product_category_id', 'image','has_variants']));
        $this->productId = $product->id;
    }
});

$addVariant = function () {
    $this->product_variants[] = [
        'id' => null,
        'name' => '',
        'price' => 0,
        'is_existing' => false,
    ];
};

$removeVariant = function ($index) {
    if ($this->has_variants && count($this->product_variants) <= 1) {
        $this->addError('product_variants', 'Debe existir al menos una variante cuando está habilitado el modo variantes.');
        return;
    }

    unset($this->product_variants[$index]);
    $this->product_variants = array_values($this->product_variants); // Reindexar
};

$save = function () {
    if ($this->isSaving) {
        return;
    }

    $this->isSaving = true;

    try {

        $this->validate();

        $data = $this->only(['name', 'descrip', 'price', 'product_category_id','has_variants']);

        if ($this->image) {
            $filename = time() . '_' . $this->image->getClientOriginalName();
            $path = $this->image->storeAs('products', $filename, 'public');
            $data['image'] = $filename;
            if ($this->productId && $this->currentImage) {
                Storage::delete('public/products/' . $this->currentImage);
            }
        }

        if ($this->productId) {
            $product = Product::find($this->productId);
            $product->update($data);
            $message = 'Producto actualizada';
        } else {
            $product  = Product::create($data);
            $product ->pos = $product ->id;
            $product ->save();
            $message = 'Producto creada';
        }

        // Guardar variantes si las hay
        if ($this->has_variants) {
            $this->saveVariants($product);
        } else {
            $product->variants()->delete();
        }

        $filters = session('filters', []);
    return $this->redirect(route('products.index', $filters), navigate: true);
        return redirect()->route('products.index', request()->query())->with('success', $message);
        return redirect('/products')->with('success', $message);

    } finally {
        $this->isSaving = false;
    }
};

$saveVariants = function ($product) {
    $existingVariantIds = collect($this->product_variants)
        ->where('is_existing', true)
        ->pluck('id')
        ->filter()
        ->toArray();

    // Eliminar variantes que ya no están en el formulario
    $product->variants()
        ->whereNotIn('id', $existingVariantIds)
        ->delete();

    // Crear o actualizar variantes
    $pos_variant = 1;
    foreach ($this->product_variants as $variantData) {
        if (isset($variantData['is_existing']) && $variantData['is_existing'] && $variantData['id']) {
            // Actualizar variante existente
            ProductVariant::where('id', $variantData['id'])
                ->update([
                    'pos'  => $pos_variant,
                    'name' => $variantData['name'],
                    'price' => $variantData['price'],
                ]);
        } else {
            // Crear nueva variante
            $product->variants()->create([
                    'pos'  => $pos_variant,
                'name' => $variantData['name'],
                'price' => $variantData['price'],
            ]);
        }
        $pos_variant++;
    }
};

// Computed para mostrar/ocultar secciones
$showVariants = computed(function () {
    return $this->has_variants == 1;
});

$showSinglePrice = computed(function () {
    return $this->has_variants == 0;
});


?>

<section class="w-full">

    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Productos</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <x-products.layout :heading="$productId ? 'Actualizar Producto' : 'Crear Producto'">
        <form wire:submit="save">
            <div class="space-y-6">

                <flux:field>
                    <flux:select label="Categoría" wire:model="product_category_id">
                            <flux:select.option value="">Selecciona categoría</flux:select.option>
                        @foreach ($categories as $id => $nombre)
                            <flux:select.option value="{{ $id }}">{{ $nombre }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:input wire:model="name" label="Nombre de Producto" placeholder="Nombre de Producto" />
                </flux:field>

                <flux:field>
                    <flux:input wire:model="descrip" label="Descripción" placeholder="Descripción de Producto" />
                </flux:field>

                <flux:field>
                    <flux:radio.group wire:model.live="has_variants" label="Opciones de Precio" variant="segmented" >
                        <flux:radio label="Único" value="0" />
                        <flux:radio label="Variantes" value="1" />
                    </flux:radio.group>
                </flux:field>

                @if($this->showVariants)
                  <div class="flex justify-end mb-4">
                        <flux:button type="button" wire:click="addVariant" variant="ghost" size="sm">
                            + Agregar Variante
                        </flux:button>
                    </div>



                        <div class="space-y-3">
                            @forelse($product_variants as $index => $variant)
                                <div class="flex items-center space-x-3 p-3 rounded border" wire:key="variant-{{ $index }}">
                                    {{-- Indicador si es variante existente --}}
                                    @if($variant['is_existing'] ?? false)
                                        <flux:badge size="sm" color="blue">Existe</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="green">Nuevo</flux:badge>
                                    @endif

                                    <div class="flex-1">
                                        <flux:input
                                            wire:model="product_variants.{{ $index }}.name"
                                            placeholder="Nombre de la variante"
                                            size="sm"
                                        />
                                        @error('product_variants.' . $index . '.name')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="w-32">
                                        <flux:input
                                            wire:model="product_variants.{{ $index }}.price"
                                            type="number"
                                            placeholder="Precio"
                                            step="0.01"
                                            min="0.1"
                                            size="sm"
                                        />
                                        @error('product_variants.' . $index . '.price')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <flux:button
                                        type="button"
                                        wire:click="removeVariant({{ $index }})"
                                        variant="danger"
                                        size="sm"
                                    >
                                        Eliminar
                                    </flux:button>
                                </div>
                            @empty
                                <div class="text-center py-4 text-gray-500">
                                    No hay variantes. Haz clic en "Agregar Variante" para comenzar.
                                </div>
                            @endforelse
                        </div>
                @endif


                @if($this->showSinglePrice)
                <flux:field>
                    <flux:input wire:model="price"  type="number" label="Precio" placeholder="0,00" inputmode="decimal" step="0.1" min="0.1" pattern="[0-9,.]*" placeholder="Precio de Producto" />
                </flux:field>
                @endif

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50">
                    <span wire:loading.remove>
                        {{ $productId ? 'Actualizar' : 'Guardar' }}
                    </span>
                    <span wire:loading>
                        Guardando...
                    </span>
                </flux:button>
            </div>
        </form>
    </x-products.layout>
</section>
