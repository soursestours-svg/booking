<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => \App\Policies\UserPolicy::class,
        Role::class => \App\Policies\RolePolicy::class,
        Permission::class => \App\Policies\PermissionPolicy::class,
        \App\Models\Service::class => \App\Policies\ServicePolicy::class,
        \App\Models\Booking::class => \App\Policies\BookingPolicy::class,
        \App\Models\Review::class => \App\Policies\ReviewPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Автоматически даем все права администраторам
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // Регистрируем все активные разрешения из базы
        $this->registerActivePermissions();
    }

    protected function registerActivePermissions(): void
    {
        try {
            // Добавляем логирование для отладки
            $permissions = Permission::active()->get();
            Log::debug('Registering permissions:', $permissions->pluck('name')->toArray());

            foreach ($permissions as $permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    // Явная проверка через getAllPermissions()
                    return $user->getAllPermissions()->contains('name', $permission->name);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to register permissions: ' . $e->getMessage());
        }
    }
}
