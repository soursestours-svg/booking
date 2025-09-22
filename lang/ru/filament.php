<?php

return [
    'brand' => 'Filament',
    'dashboard' => 'Панель управления',
    'breadcrumb' => 'Главная',
    'auth' => [
        'login' => [
            'title' => 'Вход',
            'messages' => [
                'failed' => 'Неверные учетные данные.',
            ],
        ],
    ],
    'fields' => [
        'email' => [
            'label' => 'Email',
        ],
        'password' => [
            'label' => 'Пароль',
        ],
        'remember' => [
            'label' => 'Запомнить меня',
        ],
    ],
    'buttons' => [
        'login' => [
            'label' => 'Войти',
        ],
        'logout' => [
            'label' => 'Выйти',
        ],
    ],
    'navigation' => [
        'groups' => [
            'settings' => 'Настройки',
        ],
    ],
    'resources' => [
        'pages' => [
            'index' => 'Страницы',
            'create' => 'Создать страницу',
            'edit' => 'Редактировать страницу',
        ],
        'users' => [
            'index' => 'Пользователи',
            'create' => 'Создать пользователя',
            'edit' => 'Редактировать пользователя',
        ],
    ],
];
