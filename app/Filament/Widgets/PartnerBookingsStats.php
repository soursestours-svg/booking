<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PartnerBookingsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Статистика только для услуг текущего партнера
        $totalBookings = Booking::whereHas('service', function ($query) use ($user) {
            $query->where('partner_id', $user->id);
        })->count();

        $confirmedBookings = Booking::whereHas('service', function ($query) use ($user) {
            $query->where('partner_id', $user->id);
        })->where('status', 'confirmed')->count();

        $pendingBookings = Booking::whereHas('service', function ($query) use ($user) {
            $query->where('partner_id', $user->id);
        })->where('status', 'pending')->count();

        return [
            Stat::make('Всего бронирований', $totalBookings)
                ->description('Общее количество')
                ->color('primary')
                ->icon('heroicon-o-calendar'),

            Stat::make('Подтвержденные', $confirmedBookings)
                ->description('Успешные бронирования')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Ожидают подтверждения', $pendingBookings)
                ->description('Требуют внимания')
                ->color('warning')
                ->icon('heroicon-o-clock'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()->hasRole('partner');
    }
}
