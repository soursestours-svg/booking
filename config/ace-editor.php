<?php

return [
    'default_mode' => 'html',
    'default_theme' => 'github',
    'default_dark_theme' => 'dracula',
    'default_height' => '400px',
    'default_font_size' => '20px',
    'default_font_family' => 'monospace',
    'enable_basic_autocompletion' => true,
    'enable_live_autocompletion' => true,
    'enable_snippets' => true,

    'available_modes' => [
        'html' => 'HTML',
        'css' => 'CSS',
        'javascript' => 'JavaScript',
        'php' => 'PHP',
        'blade' => 'Blade',
    ],

    'available_themes' => [
        'github' => 'GitHub',
        'monokai' => 'Monokai',
        'dracula' => 'Dracula',
        'tomorrow' => 'Tomorrow',
        'solarized_light' => 'Solarized Light',
        'solarized_dark' => 'Solarized Dark',
    ],

    // Новые настройки шрифтов :cite[3]
    'available_font_sizes' => [
        '12px' => 'Маленький',
        '14px' => 'Средний',
        '16px' => 'Большой',
        '18px' => 'Очень большой',
        '20px' => 'Гигантский',
    ],

    'available_font_families' => [
        'monospace' => 'Системный моноширинный',
        'JetBrains Mono' => 'JetBrains Mono',
        'Fira Code' => 'Fira Code',
        'Source Code Pro' => 'Source Code Pro',
        'Courier New' => 'Courier New',
        'Consolas' => 'Consolas',
    ],
];
