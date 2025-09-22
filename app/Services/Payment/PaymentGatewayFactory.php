<?php

namespace App\Services\Payment;

use Illuminate\Support\Manager;
use InvalidArgumentException;

class PaymentGatewayFactory extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return config('services.payment.default', 'demo');
    }

    /**
     * Create demo payment gateway driver
     */
    protected function createDemoDriver(): DemoPaymentGateway
    {
        return new DemoPaymentGateway();
    }

    /**
     * Create YooKassa payment gateway driver
     */
    protected function createYookassaDriver()
    {
        // В реальной реализации здесь была бы интеграция с YooKassa
        throw new InvalidArgumentException('YooKassa driver not implemented yet');
    }

    /**
     * Create Stripe payment gateway driver
     */
    protected function createStripeDriver()
    {
        // В реальной реализации здесь была бы интеграция со Stripe
        throw new InvalidArgumentException('Stripe driver not implemented yet');
    }

    /**
     * Get all available drivers
     */
    public function getDrivers(): array
    {
        return array_keys(config('services.payment.gateways', []));
    }
}
