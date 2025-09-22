<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "service_id",
        "user_id", 
        "booking_id",
        "rating",
        "comment",
        "is_approved",
        "is_visible"
    ];

    protected $casts = [
        "rating" => "integer",
        "is_approved" => "boolean",
        "is_visible" => "boolean",
        "created_at" => "datetime",
        "updated_at" => "datetime",
        "deleted_at" => "datetime",
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope для получения только одобренных отзывов
     */
    public function scopeApproved($query)
    {
        return $query->where("is_approved", true);
    }

    /**
     * Scope для получения только видимых отзывов
     */
    public function scopeVisible($query)
    {
        return $query->where("is_visible", true);
    }

    /**
     * Scope для получения отзывов по услуге
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where("service_id", $serviceId);
    }

    /**
     * Проверка, может ли пользователь оставить отзыв
     */
    public static function canUserReviewService($userId, $serviceId): bool
    {
        // Пользователь может оставить отзыв, если у него есть завершенное бронирование этой услуги
        return Booking::where("user_id", $userId)
            ->where("service_id", $serviceId)
            ->where("status", "confirmed")
            ->where("payment_status", "paid")
            ->exists();
    }
}