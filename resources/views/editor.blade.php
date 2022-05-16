<div>
    <script>
        function debounce(func, timeout = 300){
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }

        const init = function() {
            let editor = new toastui.Editor({
                el: this.$refs.editor,
                plugins: [
                    toastui.Editor.plugin.codeSyntaxHighlight,
                    toastui.Editor.plugin.tableMergedCell,
                    toastui.Editor.plugin.colorSyntax,
                ]
            });

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
    <div class="mb-6">
        <x-mailcoach::template-chooser />
    </div>

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

    <div class="form-buttons">
        <x-mailcoach::button-secondary x-on:click.prevent="$wire.renderFullHtml() && $store.modals.open('preview')" :label="__('mailcoach - Preview')"/>
        <x-mailcoach::preview-modal name="preview" :html="$fullHtml" :title="__('mailcoach - Preview') . ' - ' . $sendable->subject" />

        <x-mailcoach::button wire:click="save" :label="__('mailcoach - Save content')"/>

        <x-mailcoach::button x-on:click.prevent="$wire.save() && $store.modals.open('send-test')" class="ml-2" :label="__('mailcoach - Save and send test')"/>
        <x-mailcoach::modal name="send-test">
            <livewire:mailcoach::send-test :model="$sendable" />
        </x-mailcoach::modal>
    </div>
</div>
