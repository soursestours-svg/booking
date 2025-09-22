<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Инициировать процесс оплаты
     */
    public function create(string $locale, Booking $booking)
    {
        if ($booking->payment_status === Booking::PAYMENT_PAID) {
            return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                ->with('info', 'Бронирование уже оплачено');
        }

        try {
            // Для демо просто показываем страницу оплаты
            // В реальной системе здесь был бы редирект на платежный шлюз
            $payment = $this->paymentGateway->createPayment($booking);

            if ($payment['success']) {
                // Обновляем бронирование с информацией о платеже
                $booking->update([
                    'payment_id' => $payment['payment_id'],
                    'payment_status' => Booking::PAYMENT_PENDING,
                    'payment_method' => 'online'
                ]);
            }

            return view('payment.process', compact('booking'));

        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage());

            return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                ->with('error', 'Произошла ошибка при обработке платежа');
        }
    }

    /**
     * Обработка успешного платежа
     */
    public function success(Request $request, string $locale, Booking $booking)
    {
        try {
            $paymentId = $request->input('payment_id', $booking->payment_id);

            $result = $this->paymentGateway->confirmPayment($paymentId);

            if ($result['success'] && $result['status'] === 'succeeded') {
                $booking->update([
                    'payment_status' => Booking::PAYMENT_PAID,
                    'status' => Booking::STATUS_CONFIRMED
                ]);

                return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                    ->with('success', 'Платеж успешно завершен! Бронирование подтверждено.');
            }

            return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                ->with('error', 'Платеж не был завершен успешно');

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed: ' . $e->getMessage());

            return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                ->with('error', 'Ошибка при подтверждении платежа');
        }
    }

    /**
     * Обработка отмены платежа
     */
    public function cancel(Request $request, string $locale, Booking $booking)
    {
        try {
            $paymentId = $request->input('payment_id', $booking->payment_id);

            $result = $this->paymentGateway->cancelPayment($paymentId);

            if ($result['success']) {
                $booking->update([
                    'payment_status' => Booking::PAYMENT_FAILED,
                    'status' => Booking::STATUS_CANCELLED
                ]);

                return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                    ->with('info', 'Платеж отменен. Бронирование аннулировано.');
            }

            return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                ->with('error', 'Не удалось отменить платеж');

        } catch (\Exception $e) {
            Log::error('Payment cancellation failed: ' . $e->getMessage());

            return redirect()->route('booking.success', ['locale' => $locale, 'booking' => $booking])
                ->with('error', 'Ошибка при отмене платежа');
        }
    }

    /**
     * Вебхук для уведомлений от платежного шлюза
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();

            if (!$this->paymentGateway->verifyNotification($data)) {
                Log::warning('Invalid payment notification signature', $data);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $paymentId = $data['payment_id'] ?? null;
            if (!$paymentId) {
                return response()->json(['error' => 'Payment ID required'], 400);
            }

            $booking = Booking::where('payment_id', $paymentId)->first();
            if (!$booking) {
                Log::warning('Booking not found for payment: ' . $paymentId);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            $status = $data['status'] ?? null;
            switch ($status) {
                case 'succeeded':
                    $booking->update([
                        'payment_status' => Booking::PAYMENT_PAID,
                        'status' => Booking::STATUS_CONFIRMED
                    ]);
                    break;

                case 'failed':
                    $booking->update([
                        'payment_status' => Booking::PAYMENT_FAILED,
                        'status' => Booking::STATUS_CANCELLED
                    ]);
                    break;

                case 'refunded':
                    $booking->update([
                        'payment_status' => Booking::PAYMENT_REFUNDED
                    ]);
                    break;
            }

            Log::info('Payment webhook processed', [
                'booking_id' => $booking->id,
                'payment_id' => $paymentId,
                'status' => $status
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage(), $request->all());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Проверить статус платежа
     */
    public function status(string $locale, Booking $booking)
    {
        try {
            if (!$booking->payment_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment ID not found'
                ]);
            }

            $result = $this->paymentGateway->getPaymentStatus($booking->payment_id);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Payment status check failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to check payment status'
            ], 500);
        }
    }
}
