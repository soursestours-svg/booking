<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            editor: null,
            state: $wire.$entangle('{{ $getStatePath() }}'),
            mode: '{{ $getMode() }}',
            theme: '{{ $getTheme() }}',
            darkTheme: '{{ $getDarkTheme() }}',
            fontSize: '{{ $getFontSize() }}',
            fontFamily: '{{ $getFontFamily() }}',
            isFullscreen: false,
            init() {
                if (typeof ace === 'undefined') {
                    console.error('Ace Editor not loaded');
                    return;
                }

                this.editor = ace.edit(this.$refs.editor);
                this.editor.session.setMode('ace/mode/' + this.mode);
                this.editor.setTheme('ace/theme/' + this.theme);

                this.editor.setOptions({
                    enableBasicAutocompletion: {{ $getEnableBasicAutocompletion() ? 'true' : 'false' }},
                    enableLiveAutocompletion: {{ $getEnableLiveAutocompletion() ? 'true' : 'false' }},
                    enableSnippets: {{ $getEnableSnippets() ? 'true' : 'false' }},
                    showPrintMargin: false,
                    wrap: true,
                    autoScrollEditorIntoView: true
                });

                // Применяем настройки шрифта после инициализации
                this.editor.setOptions({
                    fontSize: this.fontSize,
                    fontFamily: this.fontFamily
                });

                this.editor.session.setValue(this.state);

                this.editor.session.on('change', () => {
                    this.state = this.editor.session.getValue();
                });

                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.attributeName === 'class') {
                            this.updateTheme();
                        }
                    });
                });

                observer.observe(document.documentElement, {
                    attributes: true
                });

                // Обработчик для сохранения по Ctrl+S
                this.editor.commands.addCommand({
                    name: 'save',
                    bindKey: {
                        win: 'Ctrl-S',
                        mac: 'Command-S'
                    },
                    exec: () => {
                        $wire.call('save', false, false); // Вызываем метод сохранения Livewire без перенаправления и без уведомления
                        setTimeout(() => {
                            this.editor.resize(); // Перерисовываем редактор после сохранения с небольшой задержкой
                        }, 100);
                    }
                });
            },
            updateTheme() {
                if (!this.editor) return;

                if (document.documentElement.classList.contains('dark')) {
                    this.editor.setTheme('ace/theme/' + this.darkTheme);
                } else {
                    this.editor.setTheme('ace/theme/' + this.theme);
                }
            },
            updateFont() {
                if (!this.editor) return;

                this.editor.setOptions({
                    fontSize: this.fontSize,
                    fontFamily: this.fontFamily
                });
            },
            toggleFullscreen() {
                this.isFullscreen = !this.isFullscreen;
                if (this.isFullscreen) {
                    document.documentElement.classList.add('ace-editor-fullscreen');
                    document.body.style.overflow = 'hidden';
                } else {
                    document.documentElement.classList.remove('ace-editor-fullscreen');
                    document.body.style.overflow = '';
                }
                this.editor.resize();
            },
            insertSnippet(code) {
                this.editor.insert(code);
            }
        }"
        wire:ignore
        x-bind:style="isFullscreen ? 'height: 100vh; width: 100vw; position: fixed; top: 0; left: 0; z-index: 10000;' : 'height: {{ $getHeight() }};'"
        x-effect="updateFont()"
    >
        <div class="flex justify-between bg-gray-100 dark:bg-gray-800 p-1">
            <div class="flex space-x-2 overflow-visible" x-show="!isFullscreen">
                @foreach ($getSnippets() as $group)
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" x-on:click="open = !open" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            {{ $group['group_name'] }}
                        </button>

                        <div x-show="open" x-on:click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                @foreach ($group['templates'] as $template)
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-500" role="menuitem"
                                       x-on:click.prevent="editor.insert('{{ str_replace(["\n", "'"], ["\\n", "\\'"], $template['code']) }}'); open = false;">
                                        {{ $template['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex space-x-2">
                <div x-data="{ open: false }" class="relative" x-show="isFullscreen">
                    <button type="button" x-on:click="open = !open" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Шаблоны
                    </button>
                    <div x-show="open" x-on:click.away="open = false" class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            @foreach ($getSnippets() as $group)
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ $group['group_name'] }}
                                </div>
                                @foreach ($group['templates'] as $template)
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-500" role="menuitem"
                                       x-on:click.prevent="editor.insert('{{ str_replace(["\n", "'"], ["\\n", "\\'"], $template['code']) }}'); open = false;">
                                        {{ $template['name'] }}
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <button type="button" x-on:click="toggleFullscreen()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg x-show="!isFullscreen" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m0 0l-5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                    <svg x-show="isFullscreen" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div
            x-ref="editor"
            style="height: calc(100% - 32px); width: 100%;"
        ></div>
    </div>

    @push('scripts')
        <!-- Подключение Ace Editor -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ace.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ext-language_tools.js"></script>

        <!-- Подключение необходимых режимов -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-php.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-html.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-javascript.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/mode-css.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.43.3/mode-php_laravel_blade.js"></script>

        <!-- Подключение тем -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/theme-github.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/theme-dracula.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/theme-monokai.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.43.3/theme-tomorrow_night_bright.js"></script>

        <!-- Подключение кастомных шрифтов (пример с JetBrains Mono) :cite[3] -->
        <style>
            @font-face {
                font-family: 'JetBrains Mono';
                src: url('https://cdn.jsdelivr.net/gh/JetBrains/JetBrainsMono/web/woff2/JetBrainsMono-Regular.woff2') format('woff2');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }

            /* Дополнительные шрифты для разработчиков */
            @font-face {
                font-family: 'Fira Code';
                src: url('https://cdn.jsdelivr.net/gh/tonsky/FiraCode@master/distr/woff2/FiraCode-Regular.woff2') format('woff2');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }

            @font-face {
                font-family: 'Source Code Pro';
                src: url('https://cdn.jsdelivr.net/fontsource/fonts/source-code-pro@latest/latin-400-normal.woff2') format('woff2');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }

            .ace-editor-fullscreen {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 9999; /* Убедитесь, что он поверх всего */
                background-color: #fff; /* Или другой фон для полноэкранного режима */
            }

            .dark .ace-editor-fullscreen {
                background-color: #1f2937; /* Темный фон для полноэкранного режима */
            }
        </style>
    @endpush
</x-dynamic-component>
