// Функция для инициализации Ace Editor с Blade автодополнением
export function initializeAceEditor(element, options) {
    const {
        statePath,
        mode,
        theme,
        darkTheme,
        fontSize,
        fontFamily,
        enableBasicAutocompletion,
        enableLiveAutocompletion,
        enableSnippets,
        height
    } = options;

    return {
        editor: null,
        state: Alpine.$data[statePath],
        mode: mode,
        theme: theme,
        darkTheme: darkTheme,
        fontSize: fontSize,
        fontFamily: fontFamily,
        bladeCompleter: null,

        init() {
            // Ждем загрузки Ace Editor
            if (typeof ace === 'undefined') {
                console.error('Ace Editor not loaded');
                return;
            }

            // Инициализация редактора
            this.editor = ace.edit(this.$refs.editor);
            this.editor.session.setMode('ace/mode/' + this.mode);
            this.editor.setTheme('ace/theme/' + this.theme);

            // Настройка параметров
            this.editor.setOptions({
                enableBasicAutocompletion: enableBasicAutocompletion,
                enableLiveAutocompletion: enableLiveAutocompletion,
                enableSnippets: enableSnippets,
                showPrintMargin: false,
                fontSize: this.fontSize,
                fontFamily: this.fontFamily,
                wrap: true,
                autoScrollEditorIntoView: true
            });

            // Инициализация кастомного completer для Blade
            this.initializeBladeCompleter();

            // Установка начального значения
            if (this.state) {
                this.editor.session.setValue(this.state);
            }

            // Обработка изменений
            this.editor.session.on('change', () => {
                this.state = this.editor.session.getValue();
            });

            // Обработка темной темы
            this.initializeThemeObserver();
        },

        initializeBladeCompleter() {
            // Создаем кастомный completer для Blade-директив
            this.bladeCompleter = {
                getCompletions: (editor, session, pos, prefix, callback) => {
                    // Определяем контекст - находимся ли мы в Blade-директиве
                    const line = session.getLine(pos.row);
                    const beforeCursor = line.substring(0, pos.column);

                    // Если перед курсором есть @ или мы начинаем вводить после @
                    if (prefix === '@' || beforeCursor.includes('@')) {
                        const bladeDirectives = [
                            {name: 'if', value: '@if($1)\n\t$0\n@endif', score: 100, meta: 'Blade Directive'},
                            {name: 'else', value: '@else', score: 90, meta: 'Blade Directive'},
                            {name: 'elseif', value: '@elseif($1)', score: 90, meta: 'Blade Directive'},
                            {name: 'endif', value: '@endif', score: 90, meta: 'Blade Directive'},
                            {name: 'foreach', value: '@foreach(${1:$array} as ${2:$element})\n\t$0\n@endforeach', score: 100, meta: 'Blade Directive'},
                            {name: 'endforeach', value: '@endforeach', score: 90, meta: 'Blade Directive'},
                            {name: 'for', value: '@for(${1:$i} = 0; $i < ${2:count}; $i++)\n\t$0\n@endfor', score: 100, meta: 'Blade Directive'},
                            {name: 'endfor', value: '@endfor', score: 90, meta: 'Blade Directive'},
                            {name: 'while', value: '@while(${1:condition})\n\t$0\n@endwhile', score: 100, meta: 'Blade Directive'},
                            {name: 'endwhile', value: '@endwhile', score: 90, meta: 'Blade Directive'},
                            {name: 'section', value: '@section(\'${1:name}\')\n\t$0\n@endsection', score: 100, meta: 'Blade Directive'},
                            {name: 'endsection', value: '@endsection', score: 90, meta: 'Blade Directive'},
                            {name: 'yield', value: '@yield(\'${1:section}\', \'${2:default}\')', score: 100, meta: 'Blade Directive'},
                            {name: 'extends', value: '@extends(\'${1:layout}\')', score: 100, meta: 'Blade Directive'},
                            {name: 'include', value: '@include(\'${1:view}\', [${2:\'key\' => \'value\'}])', score: 100, meta: 'Blade Directive'},
                            {name: 'component', value: '@component(\'${1:component}\')\n\t$0\n@endcomponent', score: 100, meta: 'Blade Directive'},
                            {name: 'endcomponent', value: '@endcomponent', score: 90, meta: 'Blade Directive'},
                            {name: 'slot', value: '@slot(\'${1:name}\')\n\t$0\n@endslot', score: 100, meta: 'Blade Directive'},
                            {name: 'endslot', value: '@endslot', score: 90, meta: 'Blade Directive'},
                            {name: 'auth', value: '@auth(\'${1:guard}\')\n\t$0\n@endauth', score: 100, meta: 'Blade Directive'},
                            {name: 'endauth', value: '@endauth', score: 90, meta: 'Blade Directive'},
                            {name: 'guest', value: '@guest(\'${1:guard}\')\n\t$0\n@endguest', score: 100, meta: 'Blade Directive'},
                            {name: 'endguest', value: '@endguest', score: 90, meta: 'Blade Directive'},
                            {name: 'push', value: '@push(\'${1:name}\')\n\t$0\n@endpush', score: 100, meta: 'Blade Directive'},
                            {name: 'endpush', value: '@endpush', score: 90, meta: 'Blade Directive'},
                            {name: 'stack', value: '@stack(\'${1:name}\')', score: 100, meta: 'Blade Directive'},
                            {name: 'php', value: '@php\n\t$0\n@endphp', score: 100, meta: 'Blade Directive'},
                            {name: 'endphp', value: '@endphp', score: 90, meta: 'Blade Directive'},
                            {name: 'verbatim', value: '@verbatim\n\t$0\n@endverbatim', score: 100, meta: 'Blade Directive'},
                            {name: 'endverbatim', value: '@endverbatim', score: 90, meta: 'Blade Directive'},
                            {name: '{{', value: '{{ $1 }}$0', score: 90, meta: 'Blade Echo'},
                            {name: '{!!', value: '{!! $1 !!}$0', score: 90, meta: 'Blade Raw Echo'},
                            {name: '{{--', value: '{{-- $1 --}}$0', score: 90, meta: 'Blade Comment'}
                        ];

                        const completions = bladeDirectives
                            .filter(item => item.name.toLowerCase().includes(prefix.toLowerCase()))
                            .map(item => ({
                                caption: item.name,
                                value: item.value,
                                score: item.score,
                                meta: item.meta
                            }));

                        callback(null, completions);
                    } else {
                        // Для обычного HTML возвращаем пустой массив
                        callback(null, []);
                    }
                }
            };

            // Добавляем completer в редактор
            const langTools = ace.require('ace/ext/language_tools');
            langTools.addCompleter(this.bladeCompleter);
        },

        initializeThemeObserver() {
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
        }
    };
}
