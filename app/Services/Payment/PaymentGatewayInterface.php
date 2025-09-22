<?php

namespace App\Services\Payment;

use App\Models\Booking;

interface PaymentGatewayInterface
{
    /**
     * Создать платеж
     */
    public function createPayment(Booking $booking, array $data = []): array;

    /**
     * Подтвердить платеж
     */
    public function confirmPayment(string $paymentId): array;

    /**
     * Отменить платеж
     */
    public function cancelPayment(string $paymentId): array;

    /**
     * Получить статус платежа
     */
    public function getPaymentStatus(string $paymentId): array;

    /**
     * Проверить подпись уведомления
     */
    public function verifyNotification(array $data): bool;
}
