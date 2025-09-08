<?php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\ProductCategory;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'categories' => ProductCategory::select('id', 'name','pos')->orderBy('pos')
                ->paginate(25)
        ];
    }

    public function moveUp($id)
    {
        $item = ProductCategory::findOrFail($id);
        $item->moveUp();
    }

    public function moveDown($id)
    {
        $item = ProductCategory::findOrFail($id);
        $item->moveDown();
    }

}; ?>


<section class="w-full">

    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1" class="mb-6">Productos</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <x-products.layout :heading="'Categorías'" >

            <flux:button variant="primary" :href="route('product-categories.create')">Crear Categorías</flux:button>

        <div class="shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($categories as $category)
                    <li class="px-6 py-2">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium ">
                                {{ $category->name }}
                            </p>
                            <div class="">
                                <button wire:click="moveUp({{ $category->id }})">⬆️</button>
                                <button wire:click="moveDown({{ $category->id }})">⬇️</button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-4 text-center">
                        No hay categorias disponibles
                    </li>
                @endforelse
            </ul>
        </div>
        <!-- Paginación -->
        <div class="mb-4">
            {{ $categories->links() }}
        </div>

    </x-settings.layout>
</section>
