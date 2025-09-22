@extends('layouts.app')

@section('title', 'Оставить отзыв: ' . $service->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('services.show', $service) }}" class="text-gray-700 hover:text-gray-900">{{ Str::limit($service->name, 20) }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="text-gray-500 ml-1 md:ml-2">Оставить отзыв</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Оставить отзыв</h1>
            <p class="text-gray-600">Поделитесь вашим опытом использования услуги</p>
            <p class="text-sm text-gray-500 mt-2">{{ $service->name }}</p>
        </div>

        <form action="{{ route('reviews.store', $service) }}" method="POST">
            @csrf

            <!-- Рейтинг -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Оценка *</label>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only" required {{ old('rating') == $i ? 'checked' : '' }}>
                            <svg class="w-8 h-8 {{ old('rating') >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Комментарий -->
            <div class="mb-6">
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Комментарий *</label>
                <textarea name="comment" id="comment" rows="5"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Расскажите о вашем опыте использования услуги..."
                          required>{{ old('comment') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Минимум 10 символов</p>
                @error('comment')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Кнопки -->
            <div class="flex space-x-4">
                <a href="{{ route('services.show', $service) }}"
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Назад
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Отправить отзыв
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Анимация выбора рейтинга
    document.querySelectorAll('input[name="rating"]').forEach(input => {
        input.addEventListener('change', function() {
            const rating = parseInt(this.value);
            document.querySelectorAll('svg.text-yellow-400').forEach(svg => {
                svg.classList.remove('text-yellow-400');
                svg.classList.add('text-gray-300');
            });

            for (let i = 1; i <= rating; i++) {
                const star = document.querySelector(`input[value="${i}"]`).nextElementSibling;
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            }
        });
    });

    // Инициализация рейтинга при загрузке
    document.addEventListener('DOMContentLoaded', function() {
        const initialRating = document.querySelector('input[name="rating"]:checked');
        if (initialRating) {
            initialRating.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
