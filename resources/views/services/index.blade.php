@extends('layouts.app')

@section('title', 'Каталог услуг - Booking Service')

@section('content')
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Каталог услуг</h1>
            <p class="text-xl text-gray-600">Выберите из множества проверенных услуг</p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-12">
            <form action="{{ route('services.search', ['locale' => app()->getLocale()]) }}" method="GET" class="relative">
                <input
                    type="text"
                    name="search"
                    placeholder="Поиск услуг..."
                    class="w-full px-6 py-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <button
                    type="submit"
                    class="absolute right-2 top-2 bg-indigo-600 text-white p-2 rounded-md hover:bg-indigo-700 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Services Grid -->
        @if($services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($services as $service)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gray-200"></div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                            <p class="text-gray-600 mb-4 line-clamp-2">{{ $service->description }}</p>

                            @if($service->partner)
                                <p class="text-sm text-gray-500 mb-4">от {{ $service->partner->name }}</p>
                            @endif

                            <div class="flex items-center mb-4">
                                <div class="flex items-center text-yellow-400 mr-2">
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
                                <span class="text-sm text-gray-600">
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
            <div class="flex justify-center">
                {{ $services->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Услуги не найдены</h3>
                <p class="text-gray-600">Попробуйте изменить параметры поиска</p>
            </div>
        @endif
    </main>
@endsection
