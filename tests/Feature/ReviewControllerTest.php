<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_review_form_for_eligible_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        // Создаем завершенное бронирование для пользователя
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($user)
            ->get(route('reviews.create', $service));

        $response->assertStatus(200);
        $response->assertViewIs('reviews.create');
        $response->assertViewHas('service', $service);
    }

    /** @test */
    public function user_cannot_view_review_form_without_completed_booking()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->get(route('reviews.create', $service));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_can_submit_review_for_eligible_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        // Создаем завершенное бронирование для пользователя
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'Excellent service with at least 10 characters',
        ];

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $service), $reviewData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'service_id' => $service->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Excellent service with at least 10 characters',
        ]);
    }

    /** @test */
    public function user_cannot_submit_review_without_completed_booking()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'Excellent service!',
        ];

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $service), $reviewData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_cannot_submit_multiple_reviews_for_same_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        // Создаем завершенное бронирование
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        // Создаем первый отзыв
        Review::factory()->create([
            'service_id' => $service->id,
            'user_id' => $user->id,
            'booking_id' => $booking->id,
        ]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'Excellent service with at least 10 characters',
        ];

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $service), $reviewData);

        $response->assertRedirect();
    }

    /** @test */
    public function rating_is_required_for_review()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $reviewData = [
            'comment' => 'Comment with at least 10 characters length',
        ];

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $service), $reviewData);

        $response->assertSessionHasErrors('rating');
    }

    /** @test */
    public function rating_must_be_between_1_and_5()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $reviewData = [
            'rating' => 6,
            'comment' => 'Comment with at least 10 characters length',
        ];

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $service), $reviewData);

        $response->assertSessionHasErrors('rating');
    }

    /** @test */
    public function comment_must_be_at_least_10_characters()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['is_active' => true]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'Short', // Меньше 10 символов
        ];

        $response = $this->actingAs($user)
            ->post(route('reviews.store', $service), $reviewData);

        $response->assertSessionHasErrors('comment');
    }
}
