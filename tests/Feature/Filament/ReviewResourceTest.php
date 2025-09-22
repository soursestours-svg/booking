<?php

namespace Tests\Feature\Filament;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_all_reviews()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $review = Review::factory()->create();

        $response = $this->actingAs($admin)
            ->get('/admin/reviews');

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_can_view_reviews_for_their_services()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');

        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $review = Review::factory()->create(['service_id' => $service->id]);
        $otherReview = Review::factory()->create();

        $response = $this->actingAs($partner)
            ->get('/partner/reviews');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_approve_review()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $review = Review::factory()->create([
            'is_approved' => false,
            'is_visible' => false
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/reviews/{$review->id}/approve");

        $response->assertRedirect();
        $this->assertTrue($review->fresh()->is_approved);
        $this->assertTrue($review->fresh()->is_visible);
    }

    /** @test */
    public function partner_can_approve_review_for_their_service()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $review = Review::factory()->create([
            'service_id' => $service->id,
            'is_approved' => false,
            'is_visible' => false
        ]);

        $response = $this->actingAs($partner)
            ->post("/partner/reviews/{$review->id}/approve");

        $response->assertRedirect();
        $this->assertTrue($review->fresh()->is_approved);
        $this->assertTrue($review->fresh()->is_visible);
    }

    /** @test */
    public function partner_cannot_approve_review_for_other_services()
    {
        $partner1 = User::factory()->create();
        $partner1->assignRole('partner');
        $partner2 = User::factory()->create();
        $partner2->assignRole('partner');

        $service = \App\Models\Service::factory()->create(['partner_id' => $partner2->id]);
        $review = Review::factory()->create(['service_id' => $service->id]);

        $response = $this->actingAs($partner1)
            ->post("/partner/reviews/{$review->id}/approve");

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_hide_review()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $review = Review::factory()->create([
            'is_approved' => true,
            'is_visible' => true
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/reviews/{$review->id}/hide");

        $response->assertRedirect();
        $this->assertFalse($review->fresh()->is_visible);
    }

    /** @test */
    public function partner_can_hide_review_for_their_service()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $review = Review::factory()->create([
            'service_id' => $service->id,
            'is_approved' => true,
            'is_visible' => true
        ]);

        $response = $this->actingAs($partner)
            ->post("/partner/reviews/{$review->id}/hide");

        $response->assertRedirect();
        $this->assertFalse($review->fresh()->is_visible);
    }

    /** @test */
    public function admin_can_delete_review()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $review = Review::factory()->create();

        $response = $this->actingAs($admin)
            ->delete("/admin/reviews/{$review->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }

    /** @test */
    public function partner_can_delete_review_for_their_service()
    {
        $partner = User::factory()->create();
        $partner->assignRole('partner');
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $review = Review::factory()->create(['service_id' => $service->id]);

        $response = $this->actingAs($partner)
            ->delete("/partner/reviews/{$review->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }

    /** @test */
    public function reviews_can_be_filtered_by_approval_status()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $approvedReview = Review::factory()->create(['is_approved' => true]);
        $pendingReview = Review::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($admin)
            ->get('/admin/reviews?tableFilters[is_approved][value]=1');

        $response->assertStatus(200);
    }

    /** @test */
    public function reviews_can_be_filtered_by_visibility()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $visibleReview = Review::factory()->create(['is_visible' => true]);
        $hiddenReview = Review::factory()->create(['is_visible' => false]);

        $response = $this->actingAs($admin)
            ->get('/admin/reviews?tableFilters[is_visible][value]=1');

        $response->assertStatus(200);
    }

    /** @test */
    public function reviews_can_be_sorted_by_rating()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $highRatingReview = Review::factory()->create(['rating' => 5]);
        $lowRatingReview = Review::factory()->create(['rating' => 1]);

        $response = $this->actingAs($admin)
            ->get('/admin/reviews?sort=rating&direction=desc');

        $response->assertStatus(200);
    }
}
