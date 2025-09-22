<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_payment_form_for_pending_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($user)
            ->get(route('payment.create', $booking));

        $response->assertStatus(200);
        $response->assertViewIs('payment.create');
        $response->assertViewHas('booking', $booking);
    }

    /** @test */
    public function user_cannot_view_payment_form_for_paid_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($user)
            ->get(route('payment.create', $booking));

        $response->assertForbidden();
    }

    /** @test */
    public function user_cannot_view_payment_form_for_other_users_booking()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user1->id,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($user2)
            ->get(route('payment.create', $booking));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_view_payment_success_page()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($user)
            ->get(route('payment.success', $booking));

        $response->assertStatus(200);
        $response->assertViewIs('payment.success');
        $response->assertViewHas('booking', $booking);
    }

    /** @test */
    public function user_can_view_payment_cancel_page()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $response = $this->actingAs($user)
            ->get(route('payment.cancel', $booking));

        $response->assertStatus(200);
        $response->assertViewIs('payment.cancel');
        $response->assertViewHas('booking', $booking);
    }

    /** @test */
    public function user_can_check_payment_status()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($user)
            ->get(route('payment.status', $booking));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'paid',
            'booking_status' => 'confirmed'
        ]);
    }

    /** @test */
    public function payment_webhook_can_process_valid_request()
    {
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_id' => 'test_payment_123'
        ]);

        $webhookData = [
            'payment_id' => 'test_payment_123',
            'status' => 'paid',
            'amount' => $booking->total_price,
            'currency' => 'RUB'
        ];

        $response = $this->post(route('payment.webhook'), $webhookData);

        $response->assertStatus(200);
        $this->assertEquals('paid', $booking->fresh()->payment_status);
        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    /** @test */
    public function payment_webhook_rejects_invalid_request()
    {
        $webhookData = [
            'payment_id' => 'invalid_payment_id',
            'status' => 'paid'
        ];

        $response = $this->post(route('payment.webhook'), $webhookData);

        $response->assertStatus(400);
    }
}
