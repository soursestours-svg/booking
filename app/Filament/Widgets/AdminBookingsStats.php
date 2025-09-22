<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminBookingsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $revenue = Booking::where('status', 'confirmed')->sum('total_price');

        return [
            Stat::make('Всего бронирований', $totalBookings)
                ->description('За все время')
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

            Stat::make('Общий доход', number_format($revenue, 0, ',', ' ') . ' ₽')
                ->description('От подтвержденных бронирований')
                ->color('success')
                ->icon('heroicon-o-currency-ruble'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
