<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_booking_form()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->get(route('booking.create', $service));

        $response->assertStatus(200);
        $response->assertViewIs('booking.create');
        $response->assertViewHas('service', $service);
    }

    /** @test */
    public function user_cannot_view_booking_form_for_inactive_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => false]);

        $response = $this->actingAs($user)
            ->get(route('booking.create', $service));

        $response->assertNotFound();
    }

    /** @test */
    public function user_can_create_booking()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $bookingData = [
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(1)->addHours(2)->format('Y-m-d H:i:s'),
            'guests_count' => 2,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '+1234567890',
            'notes' => 'Test notes',
        ];

        $response = $this->actingAs($user)
            ->post(route('booking.store', $service), $bookingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'service_id' => $service->id,
            'user_id' => $user->id,
            'customer_name' => 'Test Customer',
        ]);
    }

    /** @test */
    public function user_cannot_create_booking_for_inactive_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => false]);

        $bookingData = [
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(1)->addHours(2)->format('Y-m-d H:i:s'),
            'guests_count' => 2,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '+1234567890',
        ];

        $response = $this->actingAs($user)
            ->post(route('booking.store', $service), $bookingData);

        $response->assertNotFound();
    }

    /** @test */
    public function booking_requires_valid_data()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $bookingData = [
            'start_date' => now()->subDays(1)->format('Y-m-d H:i:s'), // Прошедшая дата
            'end_date' => now()->subDays(1)->addHours(2)->format('Y-m-d H:i:s'),
            'guests_count' => 0, // Невалидное количество гостей
        ];

        $response = $this->actingAs($user)
            ->post(route('booking.store', $service), $bookingData);

        $response->assertSessionHasErrors(['start_date', 'guests_count', 'customer_name', 'customer_email']);
    }
}
