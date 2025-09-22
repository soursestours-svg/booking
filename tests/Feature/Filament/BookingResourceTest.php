<?php

namespace Tests\Feature\Filament;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_all_bookings()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $booking = Booking::factory()->create();

        $response = $this->actingAs($admin)
            ->get('/admin/bookings');

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_can_view_bookings_for_their_services()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');

        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $booking = Booking::factory()->create(['service_id' => $service->id]);
        $otherBooking = Booking::factory()->create();

        $response = $this->actingAs($partner)
            ->get('/partner/bookings');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_booking_details()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $booking = Booking::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/admin/bookings/{$booking->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_can_view_booking_details_for_their_service()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $booking = Booking::factory()->create(['service_id' => $service->id]);

        $response = $this->actingAs($partner)
            ->get("/partner/bookings/{$booking->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_cannot_view_booking_details_for_other_services()
    {
        $partner1 = User::factory()->create();
        $partner1->assignRole('partner');
        $partner2 = User::factory()->create();
        $partner2->assignRole('partner');

        $service = \App\Models\Service::factory()->create(['partner_id' => $partner2->id]);
        $booking = Booking::factory()->create(['service_id' => $service->id]);

        $response = $this->actingAs($partner1)
            ->get("/partner/bookings/{$booking->id}");

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_update_booking_status()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $booking = Booking::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->put("/admin/bookings/{$booking->id}", [
                'status' => 'confirmed'
            ]);

        $response->assertRedirect();
        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    /** @test */
    public function partner_can_update_booking_status_for_their_service()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $booking = Booking::factory()->create([
            'service_id' => $service->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($partner)
            ->put("/partner/bookings/{$booking->id}", [
                'status' => 'confirmed'
            ]);

        $response->assertRedirect();
        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    /** @test */
    public function partner_cannot_update_booking_status_for_other_services()
    {
        $partner1 = User::factory()->create();
        $partner1->assignRole('partner');
        $partner2 = User::factory()->create();
        $partner2->assignRole('partner');

        $service = \App\Models\Service::factory()->create(['partner_id' => $partner2->id]);
        $booking = Booking::factory()->create(['service_id' => $service->id]);

        $response = $this->actingAs($partner1)
            ->put("/partner/bookings/{$booking->id}", [
                'status' => 'confirmed'
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_cancel_booking()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $booking = Booking::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->put("/admin/bookings/{$booking->id}/cancel");

        $response->assertRedirect();
        $this->assertEquals('cancelled', $booking->fresh()->status);
    }

    /** @test */
    public function partner_can_cancel_booking_for_their_service()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $booking = Booking::factory()->create([
            'service_id' => $service->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($partner)
            ->put("/partner/bookings/{$booking->id}/cancel");

        $response->assertRedirect();
        $this->assertEquals('cancelled', $booking->fresh()->status);
    }

    /** @test */
    public function booking_can_be_filtered_by_status()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $pendingBooking = Booking::factory()->create(['status' => 'pending']);
        $confirmedBooking = Booking::factory()->create(['status' => 'confirmed']);

        $response = $this->actingAs($admin)
            ->get('/admin/bookings?tableFilters[status][value]=pending');

        $response->assertStatus(200);
    }
}
