<?php

return [
    'snippets' => [
        [
            'group_name' => 'Общие шаблоны',
            'for_fields' => ['ComponentResource', 'LayoutResource', 'TemplateResource'],
            'templates' => [
                [
                    'name' => 'Blade If Statement',
                    'code' => "@if (\$condition)\n    {{-- code here --}}\n@else\n    {{-- code here --}}\n@endif",
                ],
                [
                    'name' => 'Blade Foreach Loop',
                    'code' => "@foreach (\$items as \$item)\n    {{ \$item }}\n@endforeach",
                ],
            ],
        ],
        [
            'group_name' => 'Шаблоны для компонентов',
            'for_fields' => ['ComponentResource'],
            'templates' => [
                [
                    'name' => 'Component Placeholder',
                    'code' => "<div>Component Content</div>",
                ],
            ],
        ],
        [
            'group_name' => 'Шаблоны для макетов',
            'for_fields' => ['LayoutResource'],
            'templates' => [
                [
                    'name' => 'Layout Structure',
                    'code' => "<html>\n<head>\n    <title>@yield('title')</title>\n</head>\n<body>\n    @yield('content')\n</body>\n</html>",
                ],
            ],
        ],
        [
            'group_name' => 'Шаблоны для шаблонов',
            'for_fields' => ['TemplateResource'],
            'templates' => [
                [
                    'name' => 'Template Section',
                    'code' => "@extends('layouts.app')\n\n@section('content')\n    {{-- Template specific content --}}\n@endsection",
                ],
            ],
        ],
    ],
];
