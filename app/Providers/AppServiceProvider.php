<?php

namespace App\Providers;

use App\Models\TableSchema;
use App\Observers\TableSchemaObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\PaymentGatewayInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayFactory::class, function ($app) {
            return new PaymentGatewayFactory($app);
        });

        $this->app->bind('payment.gateway', function ($app) {
            return $app->make(PaymentGatewayFactory::class)->driver();
        });

        // Привязка интерфейса к конкретной реализации через фабрику
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            return $app->make(PaymentGatewayFactory::class)->driver();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ru', 'en']) // поддерживаемые языки
                ->visible(); // показывать внутри панелей
        });
    }
}
