<?php

namespace App\Services\Payment;

use App\Models\Booking;
use Illuminate\Support\Str;

class DemoPaymentGateway implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $baseUrl = 'https://api.demo-payment.com';

    public function __construct()
    {
        $this->apiKey = config('services.payment.gateways.demo.api_key', 'demo_api_key');
        $this->secretKey = config('services.payment.gateways.demo.secret_key', 'demo_secret_key');
    }

    public function createPayment(Booking $booking, array $data = []): array
    {
        // В реальной реализации здесь был бы API запрос к платежному шлюзу
        $paymentId = 'demo_' . Str::random(16);

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'payment_url' => route('payment.create', $booking),
            'amount' => $booking->total_price,
            'currency' => 'RUB',
            'description' => "Бронирование услуги: {$booking->service->name}",
        ];
    }

    public function confirmPayment(string $paymentId): array
    {
        // Имитация успешного платежа для демо
        return [
            'success' => true,
            'payment_id' => $paymentId,
            'status' => 'succeeded',
            'amount' => 1000.00, // Демо сумма
            'currency' => 'RUB',
            'confirmed_at' => now()->toDateTimeString(),
        ];
    }

    public function cancelPayment(string $paymentId): array
    {
        // Имитация отмены платежа
        return [
            'success' => true,
            'payment_id' => $paymentId,
            'status' => 'cancelled',
            'cancelled_at' => now()->toDateTimeString(),
        ];
    }

    public function getPaymentStatus(string $paymentId): array
    {
        // Имитация проверки статуса
        $statuses = ['succeeded', 'pending', 'failed'];
        $status = $statuses[array_rand($statuses)];

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'status' => $status,
            'amount' => 1000.00,
            'currency' => 'RUB',
        ];
    }

    public function verifyNotification(array $data): bool
    {
        // В реальной реализации здесь была бы проверка подписи
        return isset($data['payment_id']) && Str::startsWith($data['payment_id'], 'demo_');
    }

    /**
     * Генерация подписи для запроса
     */
    protected function generateSignature(array $data): string
    {
        ksort($data);
        $signatureString = implode('|', $data) . $this->secretKey;
        return hash('sha256', $signatureString);
    }

    /**
     * Отправка запроса к API
     */
    protected function makeRequest(string $endpoint, array $data = []): array
    {
        // Имитация API запроса
        return [
            'success' => true,
            'data' => array_merge($data, ['demo' => true])
        ];
    }
}
