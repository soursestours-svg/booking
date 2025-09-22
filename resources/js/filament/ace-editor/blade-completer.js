// Кастомный completer для Blade-директив
export class BladeCompleter {
    constructor() {
        this.identifierRegex = /[a-zA-Z_]\w*/;
        this.bladeDirectives = this.getBladeDirectives();
    }

    getCompletions(editor, session, pos, prefix, callback) {
        if (!prefix && editor.getCursorPosition().column > 0) {
            const previousChar = session.getLine(pos.row).charAt(pos.column - 1);
            if (previousChar === '@') {
                prefix = '@';
            }
        }

        if (prefix === '@' || (prefix && prefix.startsWith('@'))) {
            const completions = this.getBladeCompletions(prefix);
            callback(null, completions);
            return;
        }

        // Для HTML-автодополнения возвращаем пустой массив
        callback(null, []);
    }

    getBladeDirectives() {
        return [
            // Основные директивы
            { name: "if", value: "@if($1)\n\t$0\n@endif", meta: "Blade Directive" },
            { name: "else", value: "@else", meta: "Blade Directive" },
            { name: "elseif", value: "@elseif($1)", meta: "Blade Directive" },
            { name: "endif", value: "@endif", meta: "Blade Directive" },

            // Циклы
            { name: "foreach", value: "@foreach(${1:$array} as ${2:$element})\n\t$0\n@endforeach", meta: "Blade Directive" },
            { name: "endforeach", value: "@endforeach", meta: "Blade Directive" },
            { name: "for", value: "@for(${1:$i} = 0; $i < ${2:count}; $i++)\n\t$0\n@endfor", meta: "Blade Directive" },
            { name: "endfor", value: "@endfor", meta: "Blade Directive" },
            { name: "while", value: "@while(${1:condition})\n\t$0\n@endwhile", meta: "Blade Directive" },
            { name: "endwhile", value: "@endwhile", meta: "Blade Directive" },

            // Секции и наследование
            { name: "section", value: "@section('${1:name}')\n\t$0\n@endsection", meta: "Blade Directive" },
            { name: "endsection", value: "@endsection", meta: "Blade Directive" },
            { name: "yield", value: "@yield('${1:section}', '${2:default}')", meta: "Blade Directive" },
            { name: "extends", value: "@extends('${1:layout}')", meta: "Blade Directive" },

            // Включения
            { name: "include", value: "@include('${1:view}', [${2:'key' => 'value'}])", meta: "Blade Directive" },
            { name: "includeIf", value: "@includeIf('${1:view}', [${2:'key' => 'value'}])", meta: "Blade Directive" },
            { name: "includeWhen", value: "@includeWhen(${1:condition}, '${2:view}', [${3:'key' => 'value'}])", meta: "Blade Directive" },

            // Компоненты
            { name: "component", value: "@component('${1:component}')\n\t$0\n@endcomponent", meta: "Blade Directive" },
            { name: "endcomponent", value: "@endcomponent", meta: "Blade Directive" },
            { name: "slot", value: "@slot('${1:name}')\n\t$0\n@endslot", meta: "Blade Directive" },
            { name: "endslot", value: "@endslot", meta: "Blade Directive" },

            // Авторизация
            { name: "auth", value: "@auth('${1:guard}')\n\t$0\n@endauth", meta: "Blade Directive" },
            { name: "endauth", value: "@endauth", meta: "Blade Directive" },
            { name: "guest", value: "@guest('${1:guard}')\n\t$0\n@endguest", meta: "Blade Directive" },
            { name: "endguest", value: "@endguest", meta: "Blade Directive" },

            // Стек и пуш
            { name: "push", value: "@push('${1:name}')\n\t$0\n@endpush", meta: "Blade Directive" },
            { name: "endpush", value: "@endpush", meta: "Blade Directive" },
            { name: "stack", value: "@stack('${1:name}')", meta: "Blade Directive" },

            // PHP код
            { name: "php", value: "@php\n\t$0\n@endphp", meta: "Blade Directive" },
            { name: "endphp", value: "@endphp", meta: "Blade Directive" },

            // Комментарии и вербатим
            { name: "verbatim", value: "@verbatim\n\t$0\n@endverbatim", meta: "Blade Directive" },
            { name: "endverbatim", value: "@endverbatim", meta: "Blade Directive" },

            // Echo конструкции
            { name: "{{", value: "{{ $1 }}$0", meta: "Blade Echo" },
            { name: "{!!", value: "{!! $1 !!}$0", meta: "Blade Raw Echo" },
            { name: "{{--", value: "{{-- $1 --}}$0", meta: "Blade Comment" }
        ];
    }

    getBladeCompletions(prefix) {
        const searchTerm = prefix.replace('@', '').toLowerCase();

        return this.bladeDirectives
            .filter(directive =>
                directive.name.toLowerCase().includes(searchTerm) ||
                directive.name.toLowerCase().startsWith(searchTerm)
            )
            .map(directive => ({
                caption: directive.name,
                value: directive.value,
                score: 1000, // Высокий приоритет для Blade-директив
                meta: directive.meta,
                completer: {
                    insertMatch: function(editor, data) {
                        editor.completer.insertMatch(data.value);
                    }
                }
            }));
    }

    getDocTooltip(item) {
        if (!item.docHTML) {
            item.docHTML = [
                "<b>", item.caption, "</b>", "<hr></hr>",
                "<div class='ace-doc-tooltip'>",
                item.meta || "Blade Directive",
                "</div>"
            ].join("");
        }
    }
}

// Экспорт для использования в других модулях
export default BladeCompleter;
