<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(["admin", "partner"]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        // Администраторы могут просматривать все отзывы
        if ($user->hasRole("admin")) {
            return true;
        }

        // Партнеры могут просматривать только отзывы на свои услуги
        if ($user->hasRole("partner")) {
            return $review->service->partner_id === $user->id;
        }

        // Пользователи могут просматривать только свои отзывы
        return $review->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole("user");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Пользователь может редактировать только свои отзывы
        // и только если они еще не одобрены
        return $review->user_id === $user->id && !$review->is_approved;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Пользователь может удалять только свои отзывы
        // Администраторы могут удалять любые отзывы
        return $review->user_id === $user->id || $user->hasRole("admin");
    }

    /**
     * Determine whether the user can approve reviews.
     */
    public function approve(User $user, Review $review): bool
    {
        // Администраторы и партнеры (только для своих услуг) могут одобрять отзывы
        if ($user->hasRole("admin")) {
            return true;
        }

        if ($user->hasRole("partner")) {
            return $review->service->partner_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Review $review): bool
    {
        return $user->hasRole("admin");
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Review $review): bool
    {
        return $user->hasRole("admin");
    }
}