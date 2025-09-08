<?php
use function Livewire\Volt\{state, rules, mount};
use App\Models\ProductCategory;
// categorias
state([
    'name' => '',

    'image' => null, // Para la nueva imagen
    'currentImage' => null,

    'categoryId' => null,
    'isSaving' => false
]);

//rules(['name' => 'required', 'price' => 'required|numeric']);
rules(['name' => 'required']);

mount(function($product_category = null) {
        info($product_category);
    if ($product_category) {
        $product_category = ProductCategory::findOrFail($product_category);
        $this->fill($product_category->only(['name', 'image']));
        $this->categoryId = $product_category->id;
    }
});

$save = function() {
    if ($this->isSaving) {
        return;
    }

    $this->isSaving = true;

    try {
        $this->validate();

        $data = $this->only(['name']);

        // Procesar la imagen si se subió una nueva
        if ($this->image) {
            // Generar nombre único para evitar conflictos
            $filename = time() . '_' . $this->image->getClientOriginalName();

            // Guardar en storage/app/public/products
            $path = $this->image->storeAs('product-categories', $filename, 'public');

            $data['image'] = $filename;

            // Si estamos editando y había una imagen anterior, eliminarla
            if ($this->categoryId && $this->currentImage) {
                Storage::delete('public/product-categories/' . $this->currentImage);
            }
        }


        if ($this->categoryId) {
            ProductCategory::find($this->categoryId)->update($data);
            $message = 'Categoría actualizada';
        } else {
            $save_category = ProductCategory::create($data);
            $save_category->pos = $save_category->id;
            $save_category->save();
            $message = 'Categoría creada';
        }
        return redirect()->route('product-categories.create')->with('success', $message);
        return redirect('/product-categories')->with('success', $message);

    } finally {
        $this->isSaving = false;
    }
};
?>

<section class="w-full">

    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Productos</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <x-products.layout :heading="$categoryId ? 'Actualizar Categoría' : 'Crear Categoría' " >
        <form wire:submit="save">
            <div class="space-y-6">
                <flux:field>
                    <flux:input
                        wire:model="name"
                        label="Nombre de Categoría"
                        placeholder="Nombre de Categoría"
                    />
                </flux:field>

                <flux:input type="file" wire:model="logo" label="Logo"/>



                <flux:button
                    variant="primary"
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                    >
                    <span wire:loading.remove>
                        {{ $categoryId ? 'Actualizar' : 'Guardar' }}
                    </span>
                    <span wire:loading>
                        Guardando...
                    </span>
                </flux:button>
            </div>
        </form>
    </x-products.layout>
</section>
