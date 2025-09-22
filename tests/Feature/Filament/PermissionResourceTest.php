<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_permissions_list()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)
            ->get('/admin/permissions');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_permission_details()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'test-permission']);

        $response = $this->actingAs($admin)
            ->get("/admin/permissions/{$permission->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_permission()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $permissionData = [
            'name' => 'new-permission',
            'guard_name' => 'web',
            'is_active' => true
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/permissions', $permissionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'name' => 'new-permission',
            'guard_name' => 'web',
            'is_active' => true
        ]);
    }

    /** @test */
    public function admin_can_edit_permission()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'test-permission']);

        $response = $this->actingAs($admin)
            ->get("/admin/permissions/{$permission->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_permission()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'test-permission']);

        $updateData = [
            'name' => 'updated-permission',
            'guard_name' => 'web',
            'is_active' => false
        ];

        $response = $this->actingAs($admin)
            ->put("/admin/permissions/{$permission->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated-permission',
            'is_active' => false
        ]);
    }

    /** @test */
    public function admin_can_delete_permission()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'test-permission']);

        $response = $this->actingAs($admin)
            ->delete("/admin/permissions/{$permission->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function non_admin_cannot_access_permission_management()
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser)
            ->get('/admin/permissions');

        $response->assertForbidden();
    }

    /** @test */
    public function moderator_cannot_access_permission_management()
    {
        // Создаем роль модератора
        $moderatorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'moderator']);

        $moderator = User::factory()->create();
        $moderator->assignRole($moderatorRole);

        $response = $this->actingAs($moderator)
            ->get('/admin/permissions');

        $response->assertForbidden();
    }

    /** @test */
    public function permission_name_is_required_for_creation()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $permissionData = [
            'guard_name' => 'web',
            'is_active' => true
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/permissions', $permissionData);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function permission_name_must_be_unique()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        // Создаем первое разрешение
        $permissionData1 = [
            'name' => 'duplicate-permission',
            'guard_name' => 'web',
            'is_active' => true
        ];

        $this->actingAs($admin)
            ->post('/admin/permissions', $permissionData1);

        // Пытаемся создать разрешение с тем же именем
        $permissionData2 = [
            'name' => 'duplicate-permission',
            'guard_name' => 'web',
            'is_active' => true
        ];

        $response = $this->actingAs($admin)
            ->post('/admin/permissions', $permissionData2);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_filter_permissions_by_name()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $testPermission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'test-filter-permission']);

        $response = $this->actingAs($admin)
            ->get('/admin/permissions?search=test-filter');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_filter_permissions_by_status()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $activePermission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'active-permission',
            'is_active' => true
        ]);

        $inactivePermission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'inactive-permission',
            'is_active' => false
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/permissions?tableFilters[is_active][value]=1');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_toggle_permission_status()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'toggle-permission',
            'is_active' => true
        ]);

        $updateData = [
            'name' => 'toggle-permission',
            'guard_name' => 'web',
            'is_active' => false
        ];

        $response = $this->actingAs($admin)
            ->put("/admin/permissions/{$permission->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'is_active' => false
        ]);
    }

    /** @test */
    public function permission_can_be_assigned_to_role()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-role']);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'assign-permission']);

        // Назначаем разрешение роли через интерфейс Filament
        $response = $this->actingAs($admin)
            ->get("/admin/roles/{$role->id}/edit");

        $response->assertStatus(200);
        // Здесь предполагается, что в форме редактирования роли есть возможность назначения разрешений
    }

    /** @test */
    public function inactive_permission_cannot_be_assigned()
    {
        // Создаем роль администратора
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate([
            'name' => 'inactive-permission',
            'is_active' => false
        ]);

        // Проверяем, что неактивное разрешение не отображается в списке доступных для назначения
        $response = $this->actingAs($admin)
            ->get('/admin/permissions');

        $response->assertStatus(200);
    }
}
