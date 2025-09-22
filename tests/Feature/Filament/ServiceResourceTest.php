<?php

namespace Tests\Feature\Filament;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_services_list()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $service = Service::factory()->create();

        $response = $this->actingAs($admin)
            ->get('/admin/services');

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_can_view_their_services()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);

        $service = Service::factory()->create(['partner_id' => $partner->id]);
        $otherService = Service::factory()->create();

        $response = $this->actingAs($partner)
            ->get('/partner/services');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_service()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $partner = User::factory()->create();

        $serviceData = [
            'name' => 'New Test Service',
            'description' => 'Test description',
            'price' => 5000,
            'is_active' => true,
            'partner_id' => $partner->id,
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/services', $serviceData);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'name' => 'New Test Service',
            'partner_id' => $partner->id,
        ]);
    }

    /** @test */
    public function partner_can_create_service_for_themselves()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);

        $serviceData = [
            'name' => 'Partner Service',
            'description' => 'Partner description',
            'price' => 3000,
            'is_active' => true,
        ];

        $response = $this->actingAs($partner)
            ->post('/partner/services', $serviceData);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'name' => 'Partner Service',
            'partner_id' => $partner->id,
        ]);
    }

    /** @test */
    public function admin_can_edit_any_service()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $service = Service::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/admin/services/{$service->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_can_edit_their_own_service()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = Service::factory()->create(['partner_id' => $partner->id]);

        $response = $this->actingAs($partner)
            ->get("/partner/services/{$service->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_cannot_edit_other_partners_service()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner1 = User::factory()->create();
        $partner1->assignRole($partnerRole);
        $partner2 = User::factory()->create();
        $partner2->assignRole($partnerRole);

        $service = Service::factory()->create(['partner_id' => $partner2->id]);

        $response = $this->actingAs($partner1)
            ->get("/partner/services/{$service->id}/edit");

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_delete_service()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $service = Service::factory()->create();

        $response = $this->actingAs($admin)
            ->delete("/admin/services/{$service->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    /** @test */
    public function partner_can_delete_their_own_service()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = Service::factory()->create(['partner_id' => $partner->id]);

        $response = $this->actingAs($partner)
            ->delete("/partner/services/{$service->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }
}
