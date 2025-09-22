<?php

namespace Database\Seeders;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Создаем роль администратора
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Создаем разрешение (если нужно)
        // Permission::firstOrCreate(['name' => 'view-admin']);

        // Находим или создаем пользователя
        $admin = User::firstOrCreate([
            'email' => 'mail@gmail.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
        ]);

        // Назначаем роль и разрешение
        $admin->assignRole($adminRole);
        $admin->givePermissionTo('view-admin');
    }
}
