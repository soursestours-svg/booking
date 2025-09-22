function aceEditor(config) {
    return {
        editor: null,
        state: null,
        config: config,
        init() {
            this.state = this.$wire.$entangle(this.config.statePath);

            // Ждем загрузки Ace Editor
            if (typeof ace === 'undefined') {
                console.error('Ace Editor not loaded');
                return;
            }

            // Инициализация редактора
            this.editor = ace.edit(this.$refs.editor);

            // Устанавливаем режим
            if (this.config.mode === 'blade') {
                this.setBladeMode();
            } else {
                this.editor.session.setMode('ace/mode/' + this.config.mode);
            }

            // Устанавливаем тему
            this.editor.setTheme('ace/theme/' + this.config.theme);

            // Настройка параметров редактора
            this.editor.setOptions({
                enableBasicAutocompletion: this.config.enableBasicAutocompletion,
                enableLiveAutocompletion: this.config.enableLiveAutocompletion,
                enableSnippets: this.config.enableSnippets,
                showPrintMargin: false,
                fontSize: this.config.fontSize,
                fontFamily: this.config.fontFamily,
                wrap: true,
                autoScrollEditorIntoView: true
            });

            // Установка начального значения
            this.editor.session.setValue(this.state);

            // Обработка изменений
            this.editor.session.on('change', () => {
                this.state = this.editor.session.getValue();
            });

            // Настройка автодополнения для Blade
            if (this.config.enableBladeAutocompletion && this.config.mode === 'blade') {
                this.setupBladeAutocompletion();
            }

            // Обработка темной темы
            this.setupThemeObserver();

            // Обновляем шрифт после инициализации
            this.updateFont();
        },

        setBladeMode() {
            try {
                this.editor.session.setMode('ace/mode/blade');
            } catch (e) {
                console.warn('Blade mode not available, falling back to HTML');
                this.editor.session.setMode('ace/mode/html');
            }
        },

        setupBladeAutocompletion() {
            const bladeCompleter = {
                getCompletions: (editor, session, pos, prefix, callback) => {
                    const completions = this.config.bladeDirectives.map(directive => ({
                        caption: '@' + directive,
                        value: '@' + directive,
                        meta: 'Blade Directive'
                    }));

                    // Добавляем HTML автодополнение
                    const htmlCompleter = ace.require('ace/ext/language_tools').snippetCompleter;
                    htmlCompleter.getCompletions(editor, session, pos, prefix, (error, htmlCompletions) => {
                        if (!error && htmlCompletions) {
                            completions.push(...htmlCompletions);
                        }
                        callback(null, completions);
                    });
                }
            };

            this.editor.completers = [bladeCompleter];
        },

        setupThemeObserver() {
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
                this.editor.setTheme('ace/theme/' + this.config.darkTheme);
            } else {
                this.editor.setTheme('ace/theme/' + this.config.theme);
            }
        },

        updateFont() {
            if (!this.editor) return;

            this.editor.setOptions({
                fontSize: this.config.fontSize,
                fontFamily: this.config.fontFamily
            });
        },

        // Метод для обновления конфигурации (если нужно динамически менять настройки)
        updateConfig(newConfig) {
            this.config = { ...this.config, ...newConfig };

            if (this.editor) {
                this.updateFont();

                if (newConfig.mode !== undefined) {
                    if (newConfig.mode === 'blade') {
                        this.setBladeMode();
                    } else {
                        this.editor.session.setMode('ace/mode/' + newConfig.mode);
                    }
                }

                if (newConfig.theme !== undefined || newConfig.darkTheme !== undefined) {
                    this.updateTheme();
                }
            }
        }
    };
}

// Глобальная функция для проверки доступности редактора
window.aceEditorInstances = {};

// Инициализация всех редакторов на странице
document.addEventListener('DOMContentLoaded', function() {
    const editors = document.querySelectorAll('[x-data^="aceEditor"]');
    editors.forEach((editor, index) => {
        window.aceEditorInstances['editor_' + index] = editor.__x;
    });
});

// Регистрация как Alpine data компонента
document.addEventListener('alpine:init', () => {
    Alpine.data('aceEditor', (config) => ({
        editor: null,
        state: null,
        config: config,
        init() {
            this.state = this.$wire.$entangle(this.config.statePath);

            // Ждем загрузки Ace Editor
            if (typeof ace === 'undefined') {
                console.error('Ace Editor not loaded');
                return;
            }

            // Инициализация редактора
            this.editor = ace.edit(this.$refs.editor);

            // Устанавливаем режим
            if (this.config.mode === 'blade') {
                this.setBladeMode();
            } else {
                this.editor.session.setMode('ace/mode/' + this.config.mode);
            }

            // Устанавливаем тему
            this.editor.setTheme('ace/theme/' + this.config.theme);

            // Настройка параметров редактора
            this.editor.setOptions({
                enableBasicAutocompletion: this.config.enableBasicAutocompletion,
                enableLiveAutocompletion: this.config.enableLiveAutocompletion,
                enableSnippets: this.config.enableSnippets,
                showPrintMargin: false,
                fontSize: this.config.fontSize,
                fontFamily: this.config.fontFamily,
                wrap: true,
                autoScrollEditorIntoView: true
            });

            // Установка начального значения
            this.editor.session.setValue(this.state);

            // Обработка изменений
            this.editor.session.on('change', () => {
                this.state = this.editor.session.getValue();
            });

            // Настройка автодополнения для Blade
            if (this.config.enableBladeAutocompletion && this.config.mode === 'blade') {
                this.setupBladeAutocompletion();
            }

            // Обработка темной темы
            this.setupThemeObserver();

            // Обновляем шрифт после инициализации
            this.updateFont();
        },

        setBladeMode() {
            try {
                this.editor.session.setMode('ace/mode/blade');
            } catch (e) {
                console.warn('Blade mode not available, falling back to HTML');
                this.editor.session.setMode('ace/mode/html');
            }
        },

        setupBladeAutocompletion() {
            const bladeCompleter = {
                getCompletions: (editor, session, pos, prefix, callback) => {
                    const completions = this.config.bladeDirectives.map(directive => ({
                        caption: '@' + directive,
                        value: '@' + directive,
                        meta: 'Blade Directive'
                    }));

                    // Добавляем HTML автодополнение
                    const htmlCompleter = ace.require('ace/ext/language_tools').snippetCompleter;
                    htmlCompleter.getCompletions(editor, session, pos, prefix, (error, htmlCompletions) => {
                        if (!error && htmlCompletions) {
                            completions.push(...htmlCompletions);
                        }
                        callback(null, completions);
                    });
                }
            };

            this.editor.completers = [bladeCompleter];
        },

        setupThemeObserver() {
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
                this.editor.setTheme('ace/theme/' + this.config.darkTheme);
            } else {
                this.editor.setTheme('ace/theme/' + this.config.theme);
            }
        },

        updateFont() {
            if (!this.editor) return;

            this.editor.setOptions({
                fontSize: this.config.fontSize,
                fontFamily: this.config.fontFamily
            });
        },

        // Метод для обновления конфигурации
        updateConfig(newConfig) {
            this.config = { ...this.config, ...newConfig };

            if (this.editor) {
                this.updateFont();

                if (newConfig.mode !== undefined) {
                    if (newConfig.mode === 'blade') {
                        this.setBladeMode();
                    } else {
                        this.editor.session.setMode('ace/mode/' + newConfig.mode);
                    }
                }

                if (newConfig.theme !== undefined || newConfig.darkTheme !== undefined) {
                    this.updateTheme();
                }
            }
        }
    }));
});
