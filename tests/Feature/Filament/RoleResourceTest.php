<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_roles_list()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)
            ->get('/admin/roles');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_role_details()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-role']);

        $response = $this->actingAs($admin)
            ->get("/admin/roles/{$role->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_role()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $roleData = [
            'name' => 'new-role',
            'guard_name' => 'web'
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/roles', $roleData);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'name' => 'new-role',
            'guard_name' => 'web'
        ]);
    }

    /** @test */
    public function admin_can_edit_role()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-role']);

        $response = $this->actingAs($admin)
            ->get("/admin/roles/{$role->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_role()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-role']);

        $updateData = [
            'name' => 'updated-role',
            'guard_name' => 'web'
        ];

        $response = $this->actingAs($admin)
            ->put("/admin/roles/{$role->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-role'
        ]);
    }

    /** @test */
    public function admin_can_delete_role()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-role']);

        $response = $this->actingAs($admin)
            ->delete("/admin/roles/{$role->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /** @test */
    public function admin_can_assign_permissions_to_role()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-role']);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'test-permission']);

        $permissionData = [
            'permissions' => ['test-permission']
        ];

        $response = $this->actingAs($admin)
            ->put("/admin/roles/{$role->id}", $permissionData);

        $response->assertRedirect();
        $this->assertTrue($role->fresh()->hasPermissionTo('test-permission'));
    }

    /** @test */
    public function non_admin_cannot_access_role_management()
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser)
            ->get('/admin/roles');

        $response->assertForbidden();
    }

    /** @test */
    public function moderator_cannot_access_role_management()
    {
        // Создаем роль модератора
        $moderatorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'moderator']);

        $moderator = User::factory()->create();
        $moderator->assignRole($moderatorRole);

        $response = $this->actingAs($moderator)
            ->get('/admin/roles');

        $response->assertForbidden();
    }

    /** @test */
    public function role_name_is_required_for_creation()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $roleData = [
            'guard_name' => 'web'
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/roles', $roleData);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function role_name_must_be_unique()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Создаем первую роль
        $roleData1 = [
            'name' => 'duplicate-role',
            'guard_name' => 'web'
        ];

        $this->actingAs($admin)
            ->post('/admin/roles', $roleData1);

        // Пытаемся создать роль с тем же именем
        $roleData2 = [
            'name' => 'duplicate-role',
            'guard_name' => 'web'
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/roles', $roleData2);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_filter_roles_by_name()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $testRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-filter-role']);

        $response = $this->actingAs($admin)
            ->get('/admin/roles?search=test-filter');

        $response->assertStatus(200);
    }
}
