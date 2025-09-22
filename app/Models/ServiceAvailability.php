<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceAvailability extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'date',
        'price',
        'available_slots',
        'is_available'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'available_slots' => 'integer',
        'is_available' => 'boolean'
    ];

    /**
     * Отношение к услуге
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope для доступных слотов
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('available_slots', '>', 0);
    }

    /**
     * Scope для конкретной даты
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope для будущих дат
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Уменьшает количество доступных слотов
     */
    public function decreaseSlots(int $count = 1): bool
    {
        if ($this->available_slots >= $count) {
            $this->available_slots -= $count;
            return $this->save();
        }

        return false;
    }

    /**
     * Увеличивает количество доступных слотов
     */
    public function increaseSlots(int $count = 1): bool
    {
        $this->available_slots += $count;
        return $this->save();
    }
}
