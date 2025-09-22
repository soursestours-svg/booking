<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UserMenuWidget extends Widget
{
    protected static string $view = 'filament.widgets.user-menu';

    public function getViewData(): array
    {
        return [
            'items' => [
                [
                    'label' => 'Мой профиль',
                    'url' => \App\Filament\Pages\UserProfile::getUrl(),
                    'icon' => 'heroicon-o-user',
                ],
                [
                    'label' => 'Выйти',
                    'url' => route('filament.admin.auth.logout'),
                    'method' => 'post',
                    'icon' => 'heroicon-o-arrow-left-on-rectangle',
                ],
            ],
        ];
    }
}
