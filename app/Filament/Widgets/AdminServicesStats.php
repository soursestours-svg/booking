<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminServicesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();
        $totalPartners = User::role('partner')->count();
        $activePartners = User::role('partner')->whereHas('services', function ($query) {
            $query->where('is_active', true);
        })->count();

        return [
            Stat::make('Всего услуг', $totalServices)
                ->description('В системе')
                ->color('primary')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Активные услуги', $activeServices)
                ->description('Доступны для бронирования')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Всего партнеров', $totalPartners)
                ->description('Зарегистрировано')
                ->color('info')
                ->icon('heroicon-o-users'),

            Stat::make('Активные партнеры', $activePartners)
                ->description('С активными услугами')
                ->color('success')
                ->icon('heroicon-o-user-group'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
