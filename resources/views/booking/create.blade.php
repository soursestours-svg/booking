@extends('layouts.app')

@section('title', 'Бронирование: ' . $service->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-gray-700 hover:text-gray-900">Главная</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="text-gray-700 hover:text-gray-900">Услуги</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('services.show', ['locale' => app()->getLocale(), 'service' => $service]) }}" class="text-gray-700 hover:text-gray-900">{{ Str::limit($service->name, 20) }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="text-gray-500 ml-1 md:ml-2">Бронирование</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Бронирование услуги</h1>
            <p class="text-gray-600">{{ $service->name }}</p>
        </div>

        <!-- Service Info -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold">{{ $service->name }}</h3>
                    <p class="text-gray-600">Цена: {{ number_format($service->price, 0, ',', ' ') }} ₽ за услугу</p>
                </div>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                    {{ $service->type === 'standard' ? 'Стандарт' : ($service->type === 'premium' ? 'Премиум' : 'VIP') }}
                </span>
            </div>
        </div>

        <!-- Booking Form -->
        <form action="{{ route('booking.store', ['locale' => app()->getLocale(), 'service' => $service]) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Dates -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Дата начала *</label>
                    <input type="date" name="start_date" id="start_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           min="{{ date('Y-m-d') }}" required>
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Дата окончания *</label>
                    <input type="date" name="end_date" id="end_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           min="{{ date('Y-m-d') }}" required>
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Guests -->
                <div>
                    <label for="guests_count" class="block text-sm font-medium text-gray-700 mb-2">Количество гостей *</label>
                    <select name="guests_count" id="guests_count"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ trans_choice('гость|гостя|гостей', $i) }}</option>
                        @endfor
                    </select>
                    @error('guests_count')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer Info -->
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Ваше имя *</label>
                    <input type="text" name="customer_name" id="customer_name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Иван Иванов" required>
                    @error('customer_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="customer_email" id="customer_email"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="ivan@example.com" required>
                    @error('customer_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                    <input type="tel" name="customer_phone" id="customer_phone"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="+7 (999) 999-99-99">
                    @error('customer_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Дополнительные пожелания</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Особые пожелания или комментарии..."></textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit"
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Подтвердить бронирование
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/imask"></script>
<script>
    // Маска для телефона
    const phoneInput = document.getElementById('customer_phone');
    if (phoneInput) {
        IMask(phoneInput, {
            mask: '+{7} (000) 000-00-00',
            lazy: false,
            placeholderChar: '_'
        });
    }

    // Автоматическое заполнение даты окончания (следующий день)
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDateInput = document.getElementById('end_date');

        if (startDate && !isNaN(startDate.getTime())) {
            // Устанавливаем дату окончания на следующий день
            const nextDay = new Date(startDate);
            nextDay.setDate(nextDay.getDate() + 1);

            // Форматируем дату в YYYY-MM-DD
            const formattedDate = nextDay.toISOString().split('T')[0];

            endDateInput.min = this.value;
            endDateInput.value = formattedDate;
        }
    });

    // Инициализация даты окончания при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if (startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            if (startDate && !isNaN(startDate.getTime())) {
                const nextDay = new Date(startDate);
                nextDay.setDate(nextDay.getDate() + 1);
                const formattedDate = nextDay.toISOString().split('T')[0];

                endDateInput.min = startDateInput.value;
                if (!endDateInput.value || endDateInput.value < startDateInput.value) {
                    endDateInput.value = formattedDate;
                }
            }
        }
    });
</script>
@endsection
