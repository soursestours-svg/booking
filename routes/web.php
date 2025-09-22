<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;

Route::prefix('{locale?}')->where(['locale' => '[a-z]{2}'])->middleware(\App\Http\Middleware\SetLocale::class)->group(function () {
    // Auth routes for public part
    Route::get('login', function() { return 'Login page'; })->name('login');
    Route::get('register', function() { return 'Register page'; })->name('register');
    Route::post('logout', function() { return 'Logout action'; })->name('logout');
    Route::get('profile', function() { return 'User profile'; })->name('profile');

    // Главная страница
    Route::get('/', [ServiceController::class, 'home'])->name('home');

    // Каталог услуг
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/search', [ServiceController::class, 'search'])->name('services.search');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

    // Процесс бронирования
    Route::get('/booking/{service}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/{service}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/success/{booking}', [BookingController::class, 'success'])->name('booking.success');

    // Платежная система
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/{booking}/create', [PaymentController::class, 'create'])->name('create');
        Route::get('/{booking}/success', [PaymentController::class, 'success'])->name('success');
        Route::get('/{booking}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        Route::get('/{booking}/status', [PaymentController::class, 'status'])->name('status');
    });

    // Система отзывов
    Route::middleware('auth')->group(function () {
        Route::resource('reviews', ReviewController::class)->only([
            'create', 'store', 'edit', 'update', 'destroy'
        ]);
        Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/hide', [ReviewController::class, 'hide'])->name('reviews.hide');
    });
});

// Webhook для платежной системы (без префикса локали)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
