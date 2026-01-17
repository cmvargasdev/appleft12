<x-layouts.app :title="__('Dashboard')">

<div class="flex items-center justify-center w-screen h-screen">

	<!-- Component Start -->
	<form class="grid grid-cols-3 gap-2 w-full max-w-screen-sm">
		<div>
			<input class="hidden" id="radio_1" type="radio" name="radio" checked>
			<label class="flex flex-col p-4 border-2 border-gray-400 cursor-pointer" for="radio_1">
				<span class="text-xs font-semibold uppercase">Small</span>
				<span class="text-xl font-bold mt-2">$10/mo</span>
				<ul class="text-sm mt-2">
					<li>Thing 1</li>
					<li>Thing 2</li>
				</ul>
			</label>
		</div>
		<div>
			<input class="hidden" id="radio_2" type="radio" name="radio">
			<label class="flex flex-col p-4 border-2 border-gray-400 cursor-pointer" for="radio_2">
				<span class="text-xs font-semibold uppercase">Medium</span>
				<span class="text-xl font-bold mt-2">$40/mo</span>
				<ul class="text-sm mt-2">
					<li>Thing 1</li>
					<li>Thing 2</li>
				</ul>
			</label>
		</div>
		<div>
			<input class="hidden" id="radio_3" type="radio" name="radio">
			<label class="flex flex-col p-4 border-2 border-gray-400 cursor-pointer" for="radio_3">
				<span class="text-xs font-semibold uppercase">Big</span>
				<span class="text-xl font-bold mt-2">$100/mo</span>
				<ul class="text-sm mt-2">
					<li>Thing 1</li>
					<li>Thing 2</li>
				</ul>
			</label>
		</div>
	</form>
	<!-- Component End  -->

</div>

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <input class="hidden" id="radio_1" type="radio" name="radio" checked>
                <label class="flex flex-col p-4 cursor-pointer" for="radio_3">
                    <span class="text-xs font-semibold uppercase">Big</span>
                    <span class="text-xl font-bold mt-2">$100/mo</span>
                    <ul class="text-sm mt-2">
                        <li>Thing 1</li>
                        <li>Thing 2</li>
                    </ul>
                </label>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
        <style>
            input:checked + label {
	border-color: black;
	box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
</x-layouts.app>
