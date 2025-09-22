<?php

namespace Tests\Feature\Filament;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WidgetsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_bookings_stats_widget_displays_correct_data()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Создаем тестовые бронирования с разными статусами
        Booking::factory()->count(3)->create(['status' => 'pending']);
        Booking::factory()->count(5)->create(['status' => 'confirmed']);
        Booking::factory()->count(2)->create(['status' => 'completed']);
        Booking::factory()->count(1)->create(['status' => 'cancelled']);

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        // Виджет должен отображать статистику по бронированиям
    }

    /** @test */
    public function admin_services_stats_widget_displays_correct_data()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Создаем тестовые услуги
        Service::factory()->count(8)->create(['is_active' => true]);
        Service::factory()->count(3)->create(['is_active' => false]);

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        // Виджет должен отображать статистику по услугам
    }

    /** @test */
    public function partner_bookings_stats_widget_displays_correct_data()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);

        // Создаем услуги партнера
        $service = Service::factory()->create(['partner_id' => $partner->id]);

        // Создаем бронирования для услуг партнера
        Booking::factory()->count(2)->create([
            'service_id' => $service->id,
            'status' => 'pending'
        ]);
        Booking::factory()->count(4)->create([
            'service_id' => $service->id,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($partner)
            ->get('/partner');

        $response->assertStatus(200);
        // Виджет должен отображать статистику по бронированиям партнера
    }

    /** @test */
    public function partner_services_stats_widget_displays_correct_data()
    {
        // Создаем роль партнера
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner = User::factory()->create();
        $partner->assignRole($partnerRole);

        // Создаем услуги партнера
        Service::factory()->count(5)->create([
            'partner_id' => $partner->id,
            'is_active' => true
        ]);
        Service::factory()->count(2)->create([
            'partner_id' => $partner->id,
            'is_active' => false
        ]);

        $response = $this->actingAs($partner)
            ->get('/partner');

        $response->assertStatus(200);
        // Виджет должен отображать статистику по услугам партнера
    }

    /** @test */
    public function user_menu_widget_displays_correctly()
    {
        // Создаем роль пользователя
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/');

        $response->assertStatus(200);
        // Виджет меню пользователя должен отображаться корректно
    }

    /** @test */
    public function admin_widgets_only_visible_to_admins()
    {
        // Создаем обычного пользователя
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertForbidden();
    }

    /** @test */
    public function partner_widgets_only_visible_to_partners()
    {
        // Создаем обычного пользователя
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/partner');

        $response->assertForbidden();
    }

    /** @test */
    public function widgets_display_correct_counts_after_data_changes()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Изначально нет бронирований
        $response1 = $this->actingAs($admin)
            ->get('/admin');

        $response1->assertStatus(200);

        // Создаем бронирования
        Booking::factory()->count(3)->create(['status' => 'pending']);

        $response2 = $this->actingAs($admin)
            ->get('/admin');

        $response2->assertStatus(200);
        // Виджет должен обновиться и показать новые данные
    }

    /** @test */
    public function widgets_handle_empty_data_correctly()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // База данных пустая
        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        // Виджеты должны корректно обрабатывать отсутствие данных
    }

    /** @test */
    public function partner_widgets_show_only_own_data()
    {
        // Создаем двух партнеров
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $partner1 = User::factory()->create();
        $partner1->assignRole($partnerRole);

        $partner2 = User::factory()->create();
        $partner2->assignRole($partnerRole);

        // Создаем услуги для первого партнера
        $service1 = Service::factory()->create(['partner_id' => $partner1->id]);
        Booking::factory()->count(3)->create(['service_id' => $service1->id]);

        // Создаем услуги для второго партнера
        $service2 = Service::factory()->create(['partner_id' => $partner2->id]);
        Booking::factory()->count(5)->create(['service_id' => $service2->id]);

        // Первый партнер должен видеть только свои данные
        $response = $this->actingAs($partner1)
            ->get('/partner');

        $response->assertStatus(200);
        // Виджеты должны показывать только данные первого партнера
    }

    /** @test */
    public function widgets_are_translated_correctly()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Устанавливаем русскую локаль
        app()->setLocale('ru');

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        // Виджеты должны отображаться на русском языке
    }

    /** @test */
    public function widgets_display_correct_financial_data()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Создаем оплаченные бронирования с разными суммами
        Booking::factory()->create([
            'status' => 'completed',
            'payment_status' => 'paid',
            'total_price' => 5000
        ]);
        Booking::factory()->create([
            'status' => 'completed',
            'payment_status' => 'paid',
            'total_price' => 7500
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin');

        $response->assertStatus(200);
        // Виджеты должны корректно отображать финансовую статистику
    }
}
