<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            editor: null,
            content: @json($content ?? null),
            init() {
                this.editor = ace.edit($refs.editor);
                this.editor.setTheme('ace/theme/{{ $getTheme() }}');
                this.editor.session.setMode('ace/mode/{{ $getLanguage() }}');
                this.editor.setReadOnly(true);
                this.editor.setValue(this.content || '', -1);

                $wire.on('update-code-viewer-{{ $getStatePath() }}', () => {
                    this.editor.setValue(this.content || '', -1);
                });
            }
        }"
        x-ref="editorContainer"
        wire:ignore
        class="border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden"
        style="height: {{ $getHeight() }};"
    >
        <div x-ref="editor" class="w-full h-full"></div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-json.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/theme-github.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/theme-tomorrow_night_bright.js" type="text/javascript" charset="utf-8"></script>
    @endpush
</x-dynamic-component>
