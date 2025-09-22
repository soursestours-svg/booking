<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('partner') || $user->hasRole('admin');
    }

    public function view(User $user, Service $service): bool
    {
        // Партнер может видеть только свои услуги, админ - все
        return $user->hasRole('admin') ||
               ($user->hasRole('partner') && $service->partner_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('partner') || $user->hasRole('admin');
    }

    public function update(User $user, Service $service): bool
    {
        // Партнер может редактировать только свои услуги, админ - все
        return $user->hasRole('admin') ||
               ($user->hasRole('partner') && $service->partner_id === $user->id);
    }

    public function delete(User $user, Service $service): bool
    {
        // Партнер может удалять только свои услуги, админ - все
        return $user->hasRole('admin') ||
               ($user->hasRole('partner') && $service->partner_id === $user->id);
    }

    public function restore(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }
}
