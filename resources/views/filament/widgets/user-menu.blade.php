<x-filament::dropdown placement="bottom-end">
    <x-slot name="trigger">
        <button type="button" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800">
            <span class="sr-only">Меню пользователя</span>
            <!--x-filament::user-avatar :user="auth()->user()" /-->
        </button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach($items as $item)
            <x-filament::dropdown.list.item
                :href="$item['url'] ?? null"
                :method="$item['method'] ?? 'get'"
                :icon="$item['icon'] ?? null"
                :color="$item['color'] ?? null"
                tag="a"
            >
                {{ $item['label'] }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
