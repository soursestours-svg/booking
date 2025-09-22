<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'user_id',
        'start_date',
        'end_date',
        'guests_count',
        'total_price',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'payment_status',
        'payment_id',
        'payment_method',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'guests_count' => 'integer',
        'total_price' => 'decimal:2'
    ];

    /**
     * Статусы бронирования
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    /**
     * Статусы платежей
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * Отношение к пользователю
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Отношение к услуге
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Отношение к отзывам
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Отношение к доступности услуги
     */
    public function availability(): BelongsTo
    {
        return $this->belongsTo(ServiceAvailability::class, 'service_availability_id');
    }

    /**
     * Scope для бронирований по статусу
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope для будущих бронирований
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
                    ->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Проверяет, можно ли отменить бронирование
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]) &&
               $this->start_date > now()->addHours(24);
    }

    /**
     * Получает длительность бронирования в часах
     */
    public function getDurationAttribute(): ?float
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInMinutes($this->end_date) / 60;
    }
}
