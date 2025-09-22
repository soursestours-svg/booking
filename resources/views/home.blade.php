@extends('layouts.app')

@section('title', __('home.title'))

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">{{ __('home.hero_title') }}</h1>
                <p class="text-xl md:text-2xl mb-8 opacity-90">{{ __('home.hero_subtitle') }}</p>

                <!-- Search Form -->
                <form action="{{ route('services.search', ['locale' => app()->getLocale()]) }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <input
                            type="text"
                            name="search"
                            placeholder="{{ __('home.search_placeholder') }}"
                            class="flex-1 px-6 py-4 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        >
                        <button
                            type="submit"
                            class="bg-indigo-800 hover:bg-indigo-900 px-8 py-4 rounded-lg font-semibold transition-colors"
                        >
                            {{ __('home.search_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Popular Services -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('home.popular_services_title') }}</h2>
                <p class="text-xl text-gray-600">{{ __('home.popular_services_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($popularServices as $service)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gray-200 bg-cover bg-center" style="background-image: url('{{ $service->main_image_url ?? 'https://via.placeholder.com/400x300.png/E2E8F0/9CA3AF?text=No+Image' }}')"></div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                            <p class="text-gray-600 mb-4 h-16 overflow-hidden">{{ Str::limit($service->description, 100) }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-indigo-600">{{ number_format($service->price, 0, ',', ' ') }} ₽</span>
                                <a href="{{ route('services.show', ['locale' => app()->getLocale(), 'service' => $service]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                                    {{ __('home.details_button') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-600 col-span-full">Популярных услуг пока нет.</p>
                @endforelse
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-md hover:bg-indigo-700 transition-colors font-semibold">
                    {{ __('home.all_services_button') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('home.features_title') }}</h2>
                <p class="text-xl text-gray-600">{{ __('home.features_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('home.feature_security_title') }}</h3>
                    <p class="text-gray-600">{{ __('home.feature_security_text') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('home.feature_time_title') }}</h3>
                    <p class="text-gray-600">{{ __('home.feature_time_text') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('home.feature_quality_title') }}</h3>
                    <p class="text-gray-600">{{ __('home.feature_quality_text') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
