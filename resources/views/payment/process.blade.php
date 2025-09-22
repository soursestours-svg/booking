@extends('layouts.app')

@section('title', 'Оплата бронирования')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                    <a href="{{ route('services.show', ['locale' => app()->getLocale(), 'service' => $booking->service]) }}" class="text-gray-700 hover:text-gray-900">{{ Str::limit($booking->service->name, 20) }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="text-gray-500 ml-1 md:ml-2">Оплата</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Оплата бронирования</h1>
            <p class="text-gray-600">Завершите процесс оплаты для подтверждения бронирования</p>
        </div>

        <!-- Booking Info -->
        <x-card class="mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-semibold">{{ $booking->service->name }}</h3>
                    <p class="text-gray-600">Период: {{ $booking->start_date->format('d.m.Y') }} - {{ $booking->end_date->format('d.m.Y') }}</p>
                    <p class="text-gray-600">Гостей: {{ $booking->guests_count }}</p>
                </div>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                    {{ $booking->service->type === 'standard' ? 'Стандарт' : ($booking->service->type === 'premium' ? 'Премиум' : 'VIP') }}
                </span>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold">Итого к оплате:</span>
                    <span class="text-2xl font-bold text-green-600">{{ number_format($booking->total_price, 0, ',', ' ') }} ₽</span>
                </div>
            </div>
        </x-card>

        <!-- Payment Methods -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Выберите способ оплаты</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Bank Card -->
                <div class="border-2 border-blue-300 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold">Банковская карта</h4>
                            <p class="text-sm text-gray-600">Visa, Mastercard, Мир</p>
                        </div>
                    </div>
                </div>

                <!-- YooMoney -->
                <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-gray-400 transition-colors opacity-50">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-400">ЮMoney</h4>
                            <p class="text-sm text-gray-400">Кошелек ЮMoney</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demo Payment Form -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">Демо-режим оплаты</h3>
            <p class="text-blue-700 mb-4">Это демонстрационная страница оплаты. В реальной системе здесь была бы интеграция с платежным шлюзом.</p>

            <div class="flex space-x-4">
                <form action="{{ route('payment.success', ['locale' => app()->getLocale(), 'booking' => $booking]) }}" method="GET">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        ✅ Успешная оплата
                    </button>
                </form>

                <form action="{{ route('payment.cancel', ['locale' => app()->getLocale(), 'booking' => $booking]) }}" method="GET">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        ❌ Отменить оплату
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Info -->
        <x-card>
            <h3 class="text-lg font-semibold mb-4">Информация о бронировании</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Имя гостя</p>
                    <p class="font-medium">{{ $booking->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium">{{ $booking->customer_email }}</p>
                </div>
                @if($booking->customer_phone)
                <div>
                    <p class="text-sm text-gray-600">Телефон</p>
                    <p class="font-medium">{{ $booking->customer_phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600">Статус оплаты</p>
                    <p class="font-medium">
                        @if($booking->payment_status === \App\Models\Booking::PAYMENT_PAID)
                            <span class="text-green-600">Оплачено</span>
                        @elseif($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING)
                            <span class="text-yellow-600">Ожидает оплаты</span>
                        @else
                            <span class="text-red-600">Не оплачено</span>
                        @endif
                    </p>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection
