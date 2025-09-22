<?php

namespace Tests\Feature\Filament;

use App\Models\ServiceAvailability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerAvailabilityCalendarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function partner_can_view_availability_calendar()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);

        $response = $this->actingAs($partner)
            ->get('/partner/availability-calendar');

        $response->assertStatus(200);
    }

    /** @test */
    public function partner_can_create_availability_slot()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        $availabilityData = [
            'service_id' => $service->id,
            'date' => '2025-09-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'is_available' => true,
            'max_clients' => 5
        ];

        $response = $this->actingAs($partner)
            ->post('/partner/availability-calendar', $availabilityData);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_availabilities', [
            'service_id' => $service->id,
            'date' => '2025-09-20',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);
    }

    /** @test */
    public function partner_can_update_availability_slot()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        $availability = ServiceAvailability::factory()->create([
            'service_id' => $service->id,
            'is_available' => true
        ]);

        $updateData = [
            'is_available' => false,
            'max_clients' => 3
        ];

        $response = $this->actingAs($partner)
            ->put("/partner/availability-calendar/{$availability->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_availabilities', [
            'id' => $availability->id,
            'is_available' => false,
            'max_clients' => 3
        ]);
    }

    /** @test */
    public function partner_can_delete_availability_slot()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        $availability = ServiceAvailability::factory()->create([
            'service_id' => $service->id
        ]);

        $response = $this->actingAs($partner)
            ->delete("/partner/availability-calendar/{$availability->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('service_availabilities', ['id' => $availability->id]);
    }

    /** @test */
    public function partner_cannot_manage_other_partners_availability()
    {
        // Создаем двух партнеров
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner1 = User::factory()->create();
        $partner1->assignRole($partnerRole);

        $partner2 = User::factory()->create();
        $partner2->assignRole($partnerRole);

        // Создаем услугу для второго партнера
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner2->id]);
        $availability = ServiceAvailability::factory()->create([
            'service_id' => $service->id
        ]);

        // Первый партнер пытается управлять доступностью второго партнера
        $response = $this->actingAs($partner1)
            ->get("/partner/availability-calendar/{$availability->id}/edit");

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_view_all_partners_availability()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)
            ->get('/admin/availability-calendar');

        $response->assertStatus(200);
    }

    /** @test */
    public function availability_slots_cannot_overlap()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        // Создаем первый слот
        ServiceAvailability::factory()->create([
            'service_id' => $service->id,
            'date' => '2025-09-20',
            'start_time' => '09:00',
            'end_time' => '10:00'
        ]);

        // Пытаемся создать перекрывающийся слот
        $overlappingData = [
            'service_id' => $service->id,
            'date' => '2025-09-20',
            'start_time' => '09:30',
            'end_time' => '10:30',
            'is_available' => true
        ];

        $response = $this->actingAs($partner)
            ->post('/partner/availability-calendar', $overlappingData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function availability_can_be_filtered_by_service()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);

        $service1 = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);
        $service2 = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        ServiceAvailability::factory()->create(['service_id' => $service1->id]);
        ServiceAvailability::factory()->create(['service_id' => $service2->id]);

        $response = $this->actingAs($partner)
            ->get("/partner/availability-calendar?service_id={$service1->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function availability_can_be_filtered_by_date_range()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        ServiceAvailability::factory()->create([
            'service_id' => $service->id,
            'date' => '2025-09-20'
        ]);
        ServiceAvailability::factory()->create([
            'service_id' => $service->id,
            'date' => '2025-09-25'
        ]);

        $response = $this->actingAs($partner)
            ->get('/partner/availability-calendar?start_date=2025-09-20&end_date=2025-09-21');

        $response->assertStatus(200);
    }

    /** @test */
    public function past_availability_cannot_be_modified()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        $pastAvailability = ServiceAvailability::factory()->create([
            'service_id' => $service->id,
            'date' => '2024-01-01', // Прошедшая дата
            'is_available' => true
        ]);

        $updateData = [
            'is_available' => false
        ];

        $response = $this->actingAs($partner)
            ->put("/partner/availability-calendar/{$pastAvailability->id}", $updateData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function availability_slots_respect_service_duration()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create([
            'partner_id' => $partner->id,
            'duration' => 60 // Длительность услуги 60 минут
        ]);

        $availabilityData = [
            'service_id' => $service->id,
            'date' => '2025-09-20',
            'start_time' => '09:00',
            'end_time' => '09:30', // Меньше длительности услуги
            'is_available' => true
        ];

        $response = $this->actingAs($partner)
            ->post('/partner/availability-calendar', $availabilityData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function availability_calendar_displays_correct_time_slots()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        // Создаем несколько слотов доступности
        ServiceAvailability::factory()->count(3)->create([
            'service_id' => $service->id,
            'date' => '2025-09-20'
        ]);

        $response = $this->actingAs($partner)
            ->get('/partner/availability-calendar?date=2025-09-20');

        $response->assertStatus(200);
        // Календарь должен отображать созданные слоты
    }

    /** @test */
    public function availability_slots_can_be_bulk_created()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);
        $service = \App\Models\Service::factory()->create(['partner_id' => $partner->id]);

        $bulkData = [
            'service_id' => $service->id,
            'start_date' => '2025-09-20',
            'end_date' => '2025-09-22',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_available' => true,
            'max_clients' => 5
        ];

        $response = $this->actingAs($partner)
            ->post('/partner/availability-calendar/bulk', $bulkData);

        $response->assertRedirect();
        // Должны быть созданы слоты на 20, 21, 22 сентября
        $this->assertDatabaseCount('service_availabilities', 3);
    }
}
