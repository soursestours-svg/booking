<?php

return [
    'fields' => [
        'search' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
        ],
    ],
    'pagination' => [
        'label' => 'Пагинация',
        'overview' => 'Показано :first по :last из :total результатов',
        'fields' => [
            'records_per_page' => [
                'label' => 'На странице',
                'options' => [
                    'all' => 'Все',
                ],
            ],
        ],
    ],
    'bulk_actions' => [
        'label' => 'Выбранные записи',
        'placeholder' => 'Действия',
        'action_for_singular_record_label' => 'для выбранной записи',
        'action_for_plural_records_label' => 'для выбранных записей',
        'actions' => [
            'delete' => [
                'label' => 'Удалить выбранные',
                'messages' => [
                    'success' => 'Записи удалены',
                    'failure' => 'Не удалось удалить записи',
                ],
            ],
        ],
    ],
    'empty_state' => [
        'label' => 'Нет :model',
        'actions' => [
            'create' => [
                'label' => 'Создать :model',
            ],
        ],
    ],
    'filters' => [
        'actions' => [
            'apply' => [
                'label' => 'Применить фильтры',
            ],
            'reset' => [
                'label' => 'Сбросить фильтры',
            ],
        ],
        'indicator' => 'Активные фильтры',
    ],
    'actions' => [
        'view' => 'Просмотр',
        'edit' => 'Редактировать',
        'delete' => 'Удалить',
        'restore' => 'Восстановить',
        'forceDelete' => 'Принудительно удалить',
        'replicate' => 'Дублировать',
    ],
];
