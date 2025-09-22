<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('partner') || $user->hasRole('admin');
    }

    public function view(User $user, Booking $booking): bool
    {
        // Партнер может видеть только бронирования своих услуг, админ - все
        return $user->hasRole('admin') ||
               ($user->hasRole('partner') && $booking->service->partner_id === $user->id);
    }

    public function create(User $user): bool
    {
        // Создание бронирований обычно доступно только пользователям через публичную часть
        return $user->hasRole('admin');
    }

    public function update(User $user, Booking $booking): bool
    {
        // Партнер может обновлять только бронирования своих услуг, админ - все
        return $user->hasRole('admin') ||
               ($user->hasRole('partner') && $booking->service->partner_id === $user->id);
    }

    public function delete(User $user, Booking $booking): bool
    {
        // Партнер может удалять только бронирования своих услуг, админ - все
        return $user->hasRole('admin') ||
               ($user->hasRole('partner') && $booking->service->partner_id === $user->id);
    }

    public function restore(User $user, Booking $booking): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return $user->hasRole('admin');
    }
}
