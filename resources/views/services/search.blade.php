@extends('layouts.app')

@section('title', 'Результаты поиска - Booking Service')

@section('content')
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-1/4">
                <form action="{{ route('services.search', ['locale' => app()->getLocale()]) }}" method="GET">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Фильтры</h2>
                        <input type="hidden" name="search" value="{{ $search ?? '' }}">

                        <!-- Price Filter -->
                        <div class="mb-6">
                            <h3 class="font-medium text-gray-900 mb-3">Цена</h3>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="price_range" value="0-1000" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(request('price_range') == '0-1000')>
                                    <span class="ml-2 text-gray-700">До 1 000 ₽</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="price_range" value="1000-3000" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(request('price_range') == '1000-3000')>
                                    <span class="ml-2 text-gray-700">1 000 - 3 000 ₽</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="price_range" value="3000-5000" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(request('price_range') == '3000-5000')>
                                    <span class="ml-2 text-gray-700">3 000 - 5 000 ₽</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="price_range" value="5000" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(request('price_range') == '5000')>
                                    <span class="ml-2 text-gray-700">От 5 000 ₽</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-6">
                            <h3 class="font-medium text-gray-900 mb-3">Рейтинг</h3>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="rating" value="4" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(request('rating') == '4')>
                                    <span class="ml-2 text-gray-700">⭐ 4+</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="rating" value="3" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(request('rating') == '3')>
                                    <span class="ml-2 text-gray-700">⭐ 3+</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors">
                                Применить фильтры
                            </button>
                            <a href="{{ route('services.search', ['locale' => app()->getLocale(), 'search' => $search ?? '']) }}" class="block w-full text-center bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors">
                                Сбросить
                            </a>
                        </div>
                    </div>
                </form>
            </aside>

            <!-- Search Results -->
            <div class="lg:w-3/4">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        Результаты поиска: "{{ $search }}"
                    </h1>
                    <p class="text-gray-600">Найдено услуг: {{ $services->total() }}</p>
                </div>

                @if($services->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($services as $service)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="h-48 bg-gray-200"></div>
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $service->description }}</p>

                                    <div class="flex items-center mb-4">
                                        <div class="flex items-center text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= round($service->reviews->avg('rating') ?? 0))
                                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">
                                            ({{ $service->reviews->count() }} отзывов)
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-indigo-600">{{ number_format($service->price, 0, ',', ' ') }} ₽</span>
                                        <a href="{{ route('services.show', ['locale' => app()->getLocale(), 'service' => $service]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                                            Подробнее
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $services->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Ничего не найдено</h3>
                        <p class="text-gray-600 mb-6">Попробуйте изменить параметры поиска или использовать другие ключевые слова</p>
                        <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition-colors font-semibold">
                            Смотреть все услуги
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection
