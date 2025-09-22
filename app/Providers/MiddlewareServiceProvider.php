<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Временно отключено из-за отсутствия модели Middleware
        // Регистрируем кастомные middleware из базы данных
        // try {
        //     $this->registerCustomMiddlewares();
        // } catch (\Exception $e) {
        //     // Игнорируем ошибки при регистрации middleware
        //     logger()->warning('Middleware registration skipped: ' . $e->getMessage());
        // }
    }

    /**
     * Register custom middlewares from files.
     */
    protected function registerCustomMiddlewares(): void
    {
        try {
            $middlewares = Middleware::where('is_active', true)->get();

            foreach ($middlewares as $middleware) {
                $middlewareName = Str::studly($middleware->name);
                $className = "App\Http\Middleware\\{$middlewareName}";

                // Проверяем, существует ли класс middleware
                if (class_exists($className)) {
                    // Регистрируем middleware
                    Route::aliasMiddleware($middleware->name, $className);
                }
            }
        } catch (\Exception $e) {
            // Логируем ошибки, но не прерываем работу приложения
            logger()->error('Error registering custom middlewares: ' . $e->getMessage());
        }
    }
}
