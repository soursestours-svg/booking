<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Создаем разрешения
        $permissions = [
            'view-admin',
            'manage-users',
            'manage-roles',
            'manage-permissions',

            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
                'is_active' => true
            ]);
        }

        // Создаем роль администратора и назначаем все разрешения
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Создаем роль модератора
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $moderatorRole->givePermissionTo(['view-admin', 'manage-users']);

        // Создаем роль партнера
        $partnerRole = Role::firstOrCreate(['name' => 'partner']);
        $partnerRole->givePermissionTo(['view-admin']);
    }
}
