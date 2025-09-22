<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_users_list()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get('/admin/users');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/admin/users/{$user->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_user()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/users', $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function admin_can_edit_user()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_user()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($admin)
            ->put("/admin/users/{$user->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->delete("/admin/users/{$user->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function admin_can_assign_roles_to_user()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $user = User::factory()->create();

        $roleData = [
            'roles' => ['partner']
        ];

        $response = $this->actingAs($admin)
            ->put("/admin/users/{$user->id}", $roleData);

        $response->assertRedirect();
        $this->assertTrue($user->fresh()->hasRole('partner'));
    }

    /** @test */
    public function admin_can_filter_users_by_role()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $partnerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'partner']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $partnerUser = User::factory()->create();
        $partnerUser->assignRole($partnerRole);

        $regularUser = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get('/admin/users?tableFilters[roles][value]=partner');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_search_users_by_name()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $user = User::factory()->create(['name' => 'John Doe']);

        $response = $this->actingAs($admin)
            ->get('/admin/users?search=John');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_search_users_by_email()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $user = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->actingAs($admin)
            ->get('/admin/users?search=john@example.com');

        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cannot_access_user_management()
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser)
            ->get('/admin/users');

        $response->assertForbidden();
    }

    /** @test */
    public function moderator_can_view_users_but_not_manage_roles()
    {
        // Создаем роль модератора
        $moderatorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'moderator']);

        $moderator = User::factory()->create();
        $moderator->assignRole($moderatorRole);

        $response = $this->actingAs($moderator)
            ->get('/admin/users');

        $response->assertStatus(200);
    }
}
