<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_view_services_list()
    {
        $service = Service::factory()->create(['is_active' => true]);

        $response = $this->get(route('services.index'));

        $response->assertStatus(200);
        $response->assertViewIs('services.index');
        $response->assertViewHas('services');
    }

    /** @test */
    public function guest_can_view_active_service_details()
    {
        $service = Service::factory()->create(['is_active' => true]);

        $response = $this->get(route('services.show', $service));

        $response->assertStatus(200);
        $response->assertViewIs('services.show');
        $response->assertViewHas('service', $service);
    }

    /** @test */
    public function guest_cannot_view_inactive_service()
    {
        $service = Service::factory()->create(['is_active' => false]);

        $response = $this->get(route('services.show', $service));

        $response->assertNotFound();
    }

    /** @test */
    public function services_are_ordered_by_creation_date()
    {
        $service1 = Service::factory()->create([
            'name' => 'First Service',
            'is_active' => true,
            'created_at' => now()->subDay()
        ]);
        $service2 = Service::factory()->create([
            'name' => 'Second Service',
            'is_active' => true,
            'created_at' => now()
        ]);

        $response = $this->get(route('services.index'));

        $response->assertStatus(200);
        $services = $response->viewData('services');
        $this->assertEquals($service2->id, $services->first()->id); // Новейший первый
    }

    /** @test */
    public function user_can_view_service_reviews()
    {
        $service = Service::factory()->create(['is_active' => true]);
        $review = \App\Models\Review::factory()->create([
            'service_id' => $service->id,
            'is_approved' => true,
            'is_visible' => true
        ]);

        $response = $this->get(route('services.show', $service));

        $response->assertStatus(200);
        $response->assertViewHas('service', function ($viewService) use ($review) {
            return $viewService->reviews->contains($review);
        });
    }

    /** @test */
    public function user_cannot_see_unapproved_reviews()
    {
        $service = Service::factory()->create(['is_active' => true]);
        $approvedReview = \App\Models\Review::factory()->create([
            'service_id' => $service->id,
            'is_approved' => true,
            'is_visible' => true
        ]);
        $unapprovedReview = \App\Models\Review::factory()->create([
            'service_id' => $service->id,
            'is_approved' => false,
            'is_visible' => false
        ]);

        $response = $this->get(route('services.show', $service));

        $response->assertStatus(200);
        $response->assertViewHas('service', function ($viewService) use ($approvedReview, $unapprovedReview) {
            return $viewService->reviews->contains($approvedReview) &&
                   !$viewService->reviews->contains($unapprovedReview);
        });
    }
}
