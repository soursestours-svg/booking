<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'partner_id',
        'name',
        'description',
        'price',
        'duration',
        'max_people',
        'is_active',
        'category_id',
        'location',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'max_people' => 'integer',
        'duration' => 'integer'
    ];

    /**
     * Отношение к партнеру (владельцу услуги)
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    /**
     * Отношение к категории
     * Временно отключено, так как таблица service_categories не существует
     */
    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(ServiceCategory::class, 'category_id');
    // }

    /**
     * Отношение к доступности (расписанию)
     */
    public function availability()
    {
        return $this->hasMany(ServiceAvailability::class);
    }

    /**
     * Отношение к бронированиям
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Отношение к отзывам
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope для активных услуг
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для услуг конкретного партнера
     */
    public function scopeForPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * Получает средний рейтинг услуги
     */
    public function getAverageRatingAttribute(): ?float
    {
        $approvedReviews = $this->reviews()->where('is_approved', true)->get();

        if ($approvedReviews->isEmpty()) {
            return null;
        }

        return round($approvedReviews->avg('rating'), 1);
    }
}
