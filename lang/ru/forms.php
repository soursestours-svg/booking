<?php

return [
    'components' => [
        'file_upload' => [
            'placeholder' => 'Перетащите файлы сюда или нажмите, чтобы выбрать.',
            'panel' => [
                'label' => 'Файлы',
            ],
            'actions' => [
                'open' => 'Открыть',
                'download' => 'Скачать',
                'edit' => 'Редактировать',
                'delete' => 'Удалить',
            ],
        ],
        'rich_editor' => [
            'toolbar_buttons' => [
                'bold' => 'Жирный',
                'italic' => 'Курсив',
                'link' => 'Ссылка',
                'blockquote' => 'Цитата',
                'bullet_list' => 'Маркированный список',
                'ordered_list' => 'Нумерованный список',
                'undo' => 'Отменить',
                'redo' => 'Повторить',
                'strike' => 'Зачеркнутый',
                'underline' => 'Подчеркнутый',
                'code' => 'Код',
                'h1' => 'Заголовок 1',
                'h2' => 'Заголовок 2',
                'h3' => 'Заголовок 3',
                'h4' => 'Заголовок 4',
                'h5' => 'Заголовок 5',
                'h6' => 'Заголовок 6',
            ],
        ],
        'select' => [
            'placeholder' => 'Выберите опцию',
            'no_search_results_message' => 'Нет результатов, соответствующих вашему поиску.',
            'loading_message' => 'Загрузка...',
            'empty_options_message' => 'Нет доступных опций.',
        ],
        'tags_input' => [
            'placeholder' => 'Новый тег',
        ],
    ],
    'fields' => [
        'search' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
        ],
    ],
    'messages' => [
        'uploaded_file_size_too_large' => 'Размер загруженного файла слишком большой.',
        'uploaded_file_not_image' => 'Загруженный файл не является изображением.',
        'uploaded_file_not_document' => 'Загруженный файл не является документом.',
        'uploaded_file_not_audio' => 'Загруженный файл не является аудио.',
        'uploaded_file_not_video' => 'Загруженный файл не является видео.',
        'uploaded_file_not_accepted_type' => 'Загруженный файл не является допустимым типом.',
    ],
];
