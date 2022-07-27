<div>
    <script>
        function debounce(func, timeout = 300){
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }

        window.init = function() {
            let editor = new toastui.Editor(Object.assign({
                el: this.$refs.editor,
                plugins: [
                    toastui.Editor.plugin.codeSyntaxHighlight,
                    toastui.Editor.plugin.tableMergedCell,
                    toastui.Editor.plugin.colorSyntax,
                ]
            }, @json(config('mailcoach-markdown-editor.options', []))));

            editor.setMarkdown(this.markdown);

            editor.addHook('change', debounce(() => {
                this.html = editor.getHTML().replaceAll('<p><br></p>', '');
                this.markdown = editor.getMarkdown();
            }));

            editor.addHook('addImageBlobHook', (blob, callback) => {
                const data = new FormData();
                data.append('file', blob);

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
                        return;
                    }

                    callback(file.url, 'alt text');
                });
            });
        }
    </script>
    @if ($model->hasTemplates())
        <div class="mb-6">
            <x-mailcoach::template-chooser />
        </div>
    @endif

    <div>
        @if($template?->containsPlaceHolders())
            <div>
                @foreach($template->placeHolderNames() as $placeHolderName)
                    <div class="form-field max-w-full mb-6" wire:key="{{ $placeHolderName }}">
                        <label class="label" for="field_{{ $placeHolderName }}">
                            {{ \Illuminate\Support\Str::of($placeHolderName)->snake(' ')->ucfirst() }}
                        </label>

                        <div wire:ignore x-data="{
                            html: @entangle('templateFieldValues.' . $placeHolderName . '.html'),
                            markdown: @entangle('templateFieldValues.' . $placeHolderName . '.markdown'),
                            init: init,
                        }">
                            <div x-ref="editor"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div>
                <label class="label" for="field_html">
                    HTML
                </label>

                <div wire:ignore x-data="{
                    html: @entangle('templateFieldValues.html.html'),
                    markdown: @entangle('templateFieldValues.html.markdown'),
                    init: init,
                }">
                    <div x-ref="editor"></div>
                </div>
            </div>
        @endif
    </div>

    <x-mailcoach::replacer-help-texts :model="$model" />
    <x-mailcoach::editor-buttons :preview-html="$fullHtml" :model="$model" />
</div>
