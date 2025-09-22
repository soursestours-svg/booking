<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Booking Service')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-2xl font-bold text-indigo-600">BookingService</a>
                </div>

                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-gray-900 hover:text-indigo-600 font-medium">{{ __('home.nav_main') }}</a>
                    <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="text-gray-900 hover:text-indigo-600 font-medium">{{ __('home.nav_services') }}</a>
                    <a href="#" class="text-gray-900 hover:text-indigo-600 font-medium">{{ __('home.nav_about') }}</a>
                    <a href="#" class="text-gray-900 hover:text-indigo-600 font-medium">{{ __('home.nav_contacts') }}</a>
                </nav>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('profile', ['locale' => app()->getLocale()]) }}" class="text-gray-900 hover:text-indigo-600 font-medium">{{ __('home.profile') }}</a>
                        <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            <button type="submit" class="text-gray-900 hover:text-indigo-600 font-medium">{{ __('home.logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-gray-900 hover:text-indigo-600 font-medium">{{ __('home.login') }}</a>
                        <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-medium">{{ __('home.register') }}</a>
                    @endauth

                    <!-- Language Switcher -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <div>{{ strtoupper(app()->getLocale()) }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1" style="display: none;">
                            @foreach(config('app.supported_locales') as $locale)
                                <a href="{{ route(Route::currentRouteName(), array_merge(Route::current()->parameters(), ['locale' => $locale])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ strtoupper($locale) }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    @yield('content')

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">BookingService</h3>
                    <p class="text-gray-400">Платформа для бронирования услуг от проверенных специалистов</p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Услуги</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Массаж</a></li>
                        <li><a href="#" class="hover:text-white">Фотографы</a></li>
                        <li><a href="#" class="hover:text-white">Репетиторы</a></li>
                        <li><a href="#" class="hover:text-white">Все услуги</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Компания</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">О нас</a></li>
                        <li><a href="#" class="hover:text-white">Контакты</a></li>
                        <li><a href="#" class="hover:text-white">Партнерам</a></li>
                        <li><a href="#" class="hover:text-white">Вакансии</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Помощь</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                        <li><a href="#" class="hover:text-white">Правила использования</a></li>
                        <li><a href="#" class="hover:text-white">Политика конфиденциальности</a></li>
                        <li><a href="#" class="hover:text-white">Поддержка</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 BookingService. Все права защищены.</p>
            </div>
        </div>
    </footer>
</body>
</html>
