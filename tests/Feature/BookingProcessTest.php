<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingProcessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can successfully book a service.
     *
     * @return void
     */
    public function test_user_can_successfully_book_a_service(): void
    {
        // 1. Arrange
        $client = User::factory()->create();
        $partner = User::factory()->create();
        $service = Service::factory()->create([
            'partner_id' => $partner->id,
            'price' => 1500, // Set a fixed price for predictable calculation
            'is_active' => true,
        ]);

        $startDate = Carbon::tomorrow();
        $endDate = Carbon::tomorrow()->addDays(3);
        $guests = 2;
        $days = $startDate->diffInDays($endDate);
        $expectedPrice = $service->price * $days * $guests;

        // 2. Act
        $response = $this->actingAs($client)->post(route('booking.store', $service), [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'guests_count' => $guests,
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
        ]);

        // 3. Assert
        $this->assertDatabaseHas('bookings', [
            'service_id' => $service->id,
            'user_id' => $client->id,
            'start_date' => $startDate->format('Y-m-d H:i:s'),
            'end_date' => $endDate->format('Y-m-d H:i:s'),
            'guests_count' => $guests,
            'total_price' => $expectedPrice,
            'status' => 'pending',
        ]);

        $booking = Booking::first();
        $response->assertRedirect(route('payment.create', $booking));
        $response->assertSessionHasNoErrors();
    }
}
