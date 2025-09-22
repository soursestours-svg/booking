<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Результаты выборки: {{ $this->record->name }}
        </x-slot>

        <div class="mb-4">
            <h3 class="text-lg font-medium">Сгенерированный запрос:</h3>
            <pre class="p-4 bg-gray-100 rounded-md dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200"><code>{{ json_encode($this->record->query_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
        </div>

        <div class="mb-4">
            <h3 class="text-lg font-medium">Количество результатов: {{ $this->results->count() }}</h3>
        </div>

        <div class="mb-4">
            <h3 class="text-lg font-medium">Результаты выборки (таблица):</h3>
            {{ $this->table }}
        </div>

        @if($this->results->isNotEmpty())
            <div class="mb-4">
                <h3 class="text-lg font-medium">Результаты выборки (JSON):</h3>
                <pre class="p-4 bg-gray-100 rounded-md dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200"><code>{{ json_encode($this->results->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
