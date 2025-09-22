@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-gray-900">Главная</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('services.index') }}" class="text-gray-700 hover:text-gray-900">Услуги</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="text-gray-500 ml-1 md:ml-2">{{ Str::limit($service->name, 30) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Image Section -->
        <div>
            @if($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}"
                     class="w-full h-96 object-cover rounded-lg shadow-md">
            @else
                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Info Section -->
        <div>
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                    {{ $service->type === 'standard' ? 'Стандарт' : ($service->type === 'premium' ? 'Премиум' : 'VIP') }}
                </span>
            </div>

            @if($service->partner)
                <p class="text-gray-600 mb-4">от {{ $service->partner->name }}</p>
            @endif

            <div class="mb-6">
                <span class="text-4xl font-bold text-gray-900">{{ number_format($service->price, 0, ',', ' ') }} ₽</span>
                <span class="text-gray-600">за услугу</span>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Описание</h3>
                <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
            </div>

            <x-card variant="primary" class="p-6 mb-6">
                <h3 class="text-lg font-semibold mb-2 text-blue-900">Хотите забронировать?</h3>
                <p class="text-blue-700 mb-4">Нажмите кнопку ниже, чтобы начать процесс бронирования</p>
                <x-button variant="primary" size="lg" :href="route('booking.create', $service)">
                    Забронировать сейчас
                </x-button>
            </x-card>

            <!-- Отзывы -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">Отзывы</h3>

                @if($reviewsCount > 0)
                    <!-- Средний рейтинг -->
                    <div class="flex items-center mb-6">
                        <div class="text-3xl font-bold text-gray-900 mr-4">
                            {{ number_format($averageRating, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600">{{ $reviewsCount }} {{ trans_choice('отзыв|отзыва|отзывов', $reviewsCount) }}</p>
                        </div>
                    </div>

                    <!-- Список отзывов -->
                    <div class="space-y-4">
                        @foreach($service->reviews as $review)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <div class="flex items-center mr-4">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $review->user->name ?? 'Анонимный пользователь' }}</span>
                                    <span class="text-sm text-gray-400 mx-2">•</span>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->format('d.m.Y') }}</span>
                                </div>
                                <p class="text-gray-700">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 border border-gray-200 rounded-lg">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-gray-600">Пока нет отзывов</p>
                        <p class="text-sm text-gray-500 mt-1">Будьте первым, кто оставит отзыв об этой услуге</p>
                    </div>
                @endif

                <!-- Кнопка оставить отзыв -->
                @auth
                    @if(Review::canUserReviewService(auth()->id(), $service->id))
                        <div class="mt-6 text-center">
                            <a href="{{ route('reviews.create', $service) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Оставить отзыв
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
