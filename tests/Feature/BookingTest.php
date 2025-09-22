<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_booking()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();

        $startDate = now()->addDays(1);
        $endDate = clone $startDate;
        $endDate->modify('+1 hour');

        $booking = Booking::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'total_price' => 1000,
            'guests_count' => 2,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '+1234567890',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'pending',
        ]);
    }

    public function test_it_belongs_to_user()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
        ]);

        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertEquals($user->id, $booking->user->id);
    }

    public function test_it_belongs_to_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
        ]);

        $this->assertInstanceOf(Service::class, $booking->service);
        $this->assertEquals($service->id, $booking->service->id);
    }

    public function test_it_can_change_status()
    {
        $booking = Booking::factory()->create(['status' => 'pending']);

        $booking->update(['status' => 'confirmed']);

        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    public function test_it_can_calculate_duration()
    {
        $startDate = now()->setTime(10, 0, 0);
        $endDate = clone $startDate;
        $endDate->modify('+2 hours 30 minutes');

        $booking = Booking::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->assertEquals(2.5, $booking->duration);
    }
}
