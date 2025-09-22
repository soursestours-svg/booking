@extends('layouts.app')

@section('title', 'Бронирование успешно создано')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center">
        <!-- Success Icon -->
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Бронирование успешно создано!</h1>
        <p class="text-gray-600 mb-8">Ваше бронирование принято в обработку. Ожидайте подтверждения.</p>
    </div>

    <!-- Booking Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Детали бронирования</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Номер бронирования</p>
                <p class="font-medium text-gray-900">#{{ $booking->id }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Статус</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Ожидание подтверждения
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-600">Услуга</p>
                <p class="font-medium text-gray-900">{{ $booking->service->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Стоимость</p>
                <p class="font-medium text-gray-900">{{ number_format($booking->total_price, 0, ',', ' ') }} ₽</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Даты</p>
                <p class="font-medium text-gray-900">
                    {{ \Carbon\Carbon::parse($booking->start_date)->format('d.m.Y') }} -
                    {{ \Carbon\Carbon::parse($booking->end_date)->format('d.m.Y') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Гостей</p>
                <p class="font-medium text-gray-900">{{ $booking->guests_count }} {{ trans_choice('гость|гостя|гостей', $booking->guests_count) }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Клиент</p>
                <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-medium text-gray-900">{{ $booking->customer_email }}</p>
            </div>

            @if($booking->customer_phone)
            <div>
                <p class="text-sm text-gray-600">Телефон</p>
                <p class="font-medium text-gray-900">{{ $booking->customer_phone }}</p>
            </div>
            @endif

            @if($booking->notes)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600">Примечания</p>
                <p class="font-medium text-gray-900">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Next Steps -->
    <div class="bg-blue-50 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">Что дальше?</h3>
        <ul class="text-blue-700 space-y-2">
            <li class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Мы свяжемся с вами в течение 24 часов для подтверждения бронирования</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Проверьте вашу электронную почту - мы отправили подтверждение</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Готовьтесь к отличному отдыху!</span>
            </li>
        </ul>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold text-center">
            Посмотреть другие услуги
        </a>
        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
           class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors font-semibold text-center">
            На главную
        </a>
    </div>
</div>
@endsection
