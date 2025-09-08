<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('products.index')" :current="request()->routeIs('products*')" wire:navigate>Productos</flux:navlist.item>
            <flux:navlist.item :href="route('product-categories.index')" :current="request()->routeIs('product-categories*')" wire:navigate>Categorías</flux:navlist.item>
            <flux:navlist.item :href="route('product-extras.index')" wire:navigate>Adicionales</flux:navlist.item>
            <flux:navlist.item :href="route('product-sides.index')" wire:navigate>Contornos</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
