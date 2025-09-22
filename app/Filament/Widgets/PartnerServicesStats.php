<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PartnerServicesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Статистика услуг текущего партнера
        $totalServices = Service::where('partner_id', $user->id)->count();
        $activeServices = Service::where('partner_id', $user->id)->where('is_active', true)->count();
        $inactiveServices = Service::where('partner_id', $user->id)->where('is_active', false)->count();

        return [
            Stat::make('Всего услуг', $totalServices)
                ->description('В каталоге')
                ->color('primary')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Активные услуги', $activeServices)
                ->description('Доступны для бронирования')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Неактивные услуги', $inactiveServices)
                ->description('Скрыты от пользователей')
                ->color('gray')
                ->icon('heroicon-o-eye-slash'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()->hasRole('partner');
    }
}
