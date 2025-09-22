<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_service()
    {
        $partner = User::factory()->create();

        $service = Service::create([
            'partner_id' => $partner->id,
            'name' => 'Test Service',
            'description' => 'Test description',
            'price' => 5000,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Test Service',
            'partner_id' => $partner->id,
            'is_active' => true,
        ]);
    }

    public function test_it_belongs_to_partner()
    {
        $partner = User::factory()->create();
        $service = Service::factory()->create(['partner_id' => $partner->id]);

        $this->assertInstanceOf(User::class, $service->partner);
        $this->assertEquals($partner->id, $service->partner->id);
    }

    public function test_it_can_be_activated_and_deactivated()
    {
        $service = Service::factory()->create(['is_active' => true]);

        $service->update(['is_active' => false]);
        $this->assertFalse($service->fresh()->is_active);

        $service->update(['is_active' => true]);
        $this->assertTrue($service->fresh()->is_active);
    }

    public function test_it_can_have_bookings()
    {
        $service = Service::factory()->create();
        $booking = \App\Models\Booking::factory()->create(['service_id' => $service->id]);

        $this->assertTrue($service->bookings->contains($booking));
        $this->assertCount(1, $service->bookings);
    }

    public function test_it_can_calculate_average_rating()
    {
        $service = Service::factory()->create();

        // Создаем отзывы с разными рейтингами
        \App\Models\Review::factory()->create([
            'service_id' => $service->id,
            'rating' => 4,
            'is_approved' => true,
        ]);

        \App\Models\Review::factory()->create([
            'service_id' => $service->id,
            'rating' => 5,
            'is_approved' => true,
        ]);

        $this->assertEquals(4.5, $service->average_rating);
    }
}
