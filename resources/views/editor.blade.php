<div class="form-grid">
    <style>
        .cm-s-easymde .cm-header-1 {
            font-size: 1.875rem
        }

        .cm-s-easymde .cm-header-2 {
            font-size: 1.5rem
        }

        .cm-s-easymde .cm-header-3 {
            font-size: 1.25rem
        }

        .cm-s-easymde .cm-header-4 {
            font-size: 1.125rem
        }

        .cm-s-easymde .cm-header-5 {
            font-size:1.125rem
        }

        .cm-s-easymde .cm-header-6 {
            font-size:1rem
        }

        .cm-s-easymde .cm-comment {
            background: none;
        }

        .cm-keyword {color: #708;}
        .cm-atom {color: #219;}
        .cm-number {color: #164;}
        .cm-def {color: #00f;}
        .cm-variable,
        .cm-punctuation,
        .cm-property,
        .cm-operator {}
        .cm-variable-2 {color: #05a;}
        .cm-formatting-list, .cm-formatting-list + .cm-variable-2 {color: #000;}
        .cm-variable-3, .cm-s-default .cm-type {color: #085;}
        .cm-comment {color: #a50;}
        .cm-string {color: #a11;}
        .cm-string-2 {color: #f50;}
        .cm-meta {color: #555;}
        .cm-qualifier {color: #555;}
        .cm-builtin {color: #30a;}
        .cm-bracket {color: #997;}
        .cm-tag {color: #170;}
        .cm-attribute {color: #00c;}
        .cm-hr {color: #999;}
        .cm-link {color: #00c;}
    </style>
    <script>
        function debounce(func, timeout = 300){
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }

        window.init = function() {
            let editor = new EasyMDE({
                autoDownloadFontAwesome: false,
                element: this.$refs.editor,
                uploadImage: true,
                placeholder: '{{ __mc('Start writing…') }}',
                initialValue: this.markdown,
                spellChecker: false,
                autoSave: false,
                status: [{
                            className: "upload-image",
                            defaultValue: ''
                        }],
                toolbar: [
                    "heading", "bold", "italic", "link",
                    "|",
                    "quote", "unordered-list", "ordered-list", "table",
                    "|",
                    {
                        name: "upload-image",
                        action: EasyMDE.drawUploadedImage,
                        className: "fa fa-image",
                    },
                    "undo",
                    { // When FontAwesome is not auto downloaded, this loads the correct icon
                        name: "redo",
                        action: EasyMDE.redo,
                        className: "fa fa-redo",
                        title: "Redo",
                    },
                ],
                imageAccept: 'image/png, image/jpeg, image/gif, image/avif',
                imageUploadFunction: function(file, onSuccess, onError) {
                    if (file.size > 1024 * 1024 * 2) {
                        return onError('File cannot be larger than 2MB.');
                    }

                    if (file.type.split('/')[0] !== 'image') {
                        return onError('File must be an image.');
                    }

                    const data = new FormData();
                    data.append('file', file);

                    fetch('{{ action(\Spatie\Mailcoach\Http\Api\Controllers\UploadsController::class) }}', {
                        method: 'POST',
                        body: data,
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}',
                        },
                    })
                        .then(response => response.json())
                        .then(({ success, file }) => {
                            if (! success) {
                                return onError();
                            }

                            onSuccess(file.url);
                        });
                },
            });

            editor.codemirror.on("change", debounce(() => {
                this.markdown = editor.value();
                this.$refs.editor.dirty = true;
                renderToHtml(this);
            }));

            this.$watch('theme', () => {
                this.markdown = editor.value();
                renderToHtml(this);
            });

            function renderToHtml(instance) {
                fetch('{{ route('mailcoach-markdown-editor.render-markdown') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        markdown: instance.markdown,
                        theme: instance.theme,
                    })
                })
                    .then(response => response.text())
                    .then(html => instance.html = html);
            }
        }
    </script>
    @if ($model->hasTemplates())
        <x-mailcoach::template-chooser :clearable="false" />
    @endif

    @foreach($template?->fields() ?? [['name' => 'html', 'type' => 'editor']] as $field)
        <x-mailcoach::editor-fields :name="$field['name']" :type="$field['type']" :label="$field['name'] === 'html' ? 'Markdown' : null">
            <x-slot name="editor">
                <div class="markup markup-editor markup-lists markup-links markup-code"
                    wire:ignore x-data="{
                    html: @entangle('templateFieldValues.' . $field['name'] . '.html'),
                    markdown: @entangle('templateFieldValues.' . $field['name'] . '.markdown'),
                    theme: @entangle('templateFieldValues.' . $field['name'] . '.theme'),
                    init: init,
                }">
                    <textarea x-ref="editor" data-dirty-check></textarea>

                    <div class="form-field -mt-4 mb-4" x-show="markdown.includes('```')" x-cloak>
                        <label class="label" for="theme">
                            Syntax highlighting theme
                        </label>
                        <div class="select">
                            <select class="" name="theme" id="theme" x-model="theme">
                                <option>dark-plus</option>
                                <option>dracula-soft</option>
                                <option>dracula</option>
                                <option>github-dark-dimmed</option>
                                <option>github-dark</option>
                                <option>github-light</option>
                                <option>hc_light</option>
                                <option>light-plus</option>
                                <option>material-darker</option>
                                <option>material-default</option>
                                <option>material-lighter</option>
                                <option>material-ocean</option>
                                <option>material-palenight</option>
                                <option>min-dark</option>
                                <option>min-light</option>
                                <option>monokai</option>
                                <option>nord</option>
                                <option>one-dark-pro</option>
                                <option>poimandres</option>
                                <option>rose-pine-dawn</option>
                                <option>rose-pine-moon</option>
                                <option>rose-pine</option>
                                <option>slack-dark</option>
                                <option>slack-ochin</option>
                                <option>solarized-dark</option>
                                <option>solarized-light</option>
                                <option>vitesse-dark</option>
                                <option>vitesse-light</option>
                            </select>
                            <div class="select-arrow">
                                <i class="fas fa-angle-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-mailcoach::editor-fields>
    @endforeach

    <div class="-mt-4 flex gap-4">
        <x-mailcoach::replacer-help-texts :model="$model" />
        <a class="link-dimmed" href="https://www.markdownguide.org/basic-syntax/" target="_blank">Markup syntax</a>
    </div>
    <x-mailcoach::editor-buttons :preview-html="$fullHtml" :model="$model" />
</div>
