<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем роли и права доступа
        $this->call(PermissionSeeder::class);

        // Создаем администратора
        $this->call(AdminUserSeeder::class);

        // Создаем партнеров
        User::factory()->count(5)->create()->each(function ($user) {
            $user->assignRole('partner');
        });

        // Создаем услуги
        $this->call(ServiceSeeder::class);
    }
}
