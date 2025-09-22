@extends('layouts.app')

@section('title', $service->name . ' - Booking Service')

@section('content')
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-gray-700 hover:text-gray-900">Главная</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
                        </svg>
                        <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="text-gray-700 hover:text-gray-900">Услуги</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">{{ Str::limit($service->name, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Image Gallery -->
            <div>
                <div class="w-full h-96 bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="grid grid-cols-4 gap-2">
                    @for($i = 0; $i < 4; $i++)
                        <div class="h-20 bg-gray-300 rounded"></div>
                    @endfor
                </div>
            </div>

            <!-- Service Info -->
            <div>
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                    <span class="bg-indigo-100 text-indigo-800 text-sm font-medium px-3 py-1 rounded-full">
                        {{ $service->is_active ? 'Активно' : 'Неактивно' }}
                    </span>
                </div>

                @if($service->partner)
                    <p class="text-gray-600 mb-4">Предоставляется: {{ $service->partner->name }}</p>
                @endif

                <!-- Rating -->
                <div class="flex items-center mb-6">
                    <div class="flex items-center text-yellow-400 mr-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($averageRating))
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-gray-600">({{ $reviewsCount }} отзывов)</span>
                </div>

                <!-- Price -->
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900">{{ number_format($service->price, 0, ',', ' ') }} ₽</span>
                    <span class="text-gray-600">за услугу</span>
                </div>

                <!-- Booking Sticky Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Забронировать услугу</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Цена:</span>
                            <span class="font-semibold">{{ number_format($service->price, 0, ',', ' ') }} ₽</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Длительность:</span>
                            <span class="font-semibold">{{ $service->duration }} минут</span>
                        </div>
                    </div>
                    <a href="{{ route('booking.create', ['locale' => app()->getLocale(), 'service' => $service]) }}"
                       class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 transition-colors font-semibold mt-4 block text-center">
                        Забронировать сейчас
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="mt-12">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600">
                        Описание
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Удобства
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Отзывы
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="py-6">
                <!-- Description Tab -->
                <div class="tab-content active">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Описание услуги</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
                </div>

                <!-- Amenities Tab -->
                <div class="tab-content hidden">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Удобства</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Бесплатная отмена</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Подтверждение сразу</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Опытный специалист</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Все необходимое оборудование</span>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-content hidden">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Отзывы</h3>

                    @if($reviewsCount > 0)
                        <div class="space-y-6">
                            @foreach($service->reviews as $review)
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <div class="flex items-center mb-4">
                                        <div class="flex items-center text-yellow-400 mr-4">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $review->user->name ?? 'Аноним' }}</span>
                                        <span class="text-sm text-gray-400 mx-2">•</span>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->format('d.m.Y') }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-gray-600">Пока нет отзывов</p>
                            <p class="text-sm text-gray-500 mt-1">Будьте первым, кто оставит отзыв</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach((button, index) => {
                button.addEventListener('click', () => {
                    // Remove active classes
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-indigo-500', 'text-indigo-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });
                    tabContents.forEach(content => content.classList.add('hidden'));

                    // Add active classes
                    button.classList.remove('border-transparent', 'text-gray-500');
                    button.classList.add('border-indigo-500', 'text-indigo-600');
                    tabContents[index].classList.remove('hidden');
                });
            });
        });
    </script>
@endsection
