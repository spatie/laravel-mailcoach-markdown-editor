@push('endHead')
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/3.1.5/toastui-editor.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/editor-plugin-code-syntax-highlight/3.0.0/toastui-editor-plugin-code-syntax-highlight.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/editor-plugin-table-merged-cell/3.0.1/toastui-editor-plugin-table-merged-cell.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/editor-plugin-color-syntax/latest/toastui-editor-plugin-color-syntax.min.css" />
@endpush
@push('modals')
    <script src="https://uicdn.toast.com/chart/latest/toastui-chart.min.js"></script>
    <script src="https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.min.js"></script>
    <script src="https://uicdn.toast.com/editor/3.1.5/toastui-editor-all.min.js"></script>
    <script src="https://uicdn.toast.com/editor-plugin-code-syntax-highlight/3.0.0/toastui-editor-plugin-code-syntax-highlight-all.min.js"></script>
    <script src="https://uicdn.toast.com/editor-plugin-table-merged-cell/3.0.1/toastui-editor-plugin-table-merged-cell.min.js"></script>
    <script src="https://uicdn.toast.com/editor-plugin-color-syntax/3.0.3/toastui-editor-plugin-color-syntax.min.js"></script>
    @php
        try {
            $manifest = json_decode(file_get_contents(public_path('vendor/mailcoach-markdown-editor/manifest.json')), true);
        } catch (Exception $e) {
            $manifest = null;
        }
    @endphp
    @if ($manifest)
        <script type="module" src="/vendor/mailcoach-markdown-editor/{{ $manifest['resources/js/editor.js']['file'] }}"></script>
    @else
        <script type="module" src="http://localhost:3000/@vite/client"></script>
        <script type="module" src="http://localhost:3000/resources/js/editor.js"></script>
    @endif
@endpush
<div class="border rounded-md">
    <div class="bg-white min-h-full rounded-md">
        <div id="markdown-editor"
             data-options="{{ json_encode(config('mailcoach-markdown-editor.options')) }}"
             data-structured-html="{{ $body ?? '' }}"
             data-upload="{{ action([\Spatie\MailcoachMarkdownEditor\Http\Controllers\EditorController::class, 'upload']) }}"
        ></div>
        @error('html')
            <p class="form-error" role="alert">{{ $message }}</p>
        @enderror
    </div>
</div>

<input type="hidden" id="body" name="structured_html[body]" value="{{ old('structured_html.body', $body ?? '') }}">
<input type="hidden" id="template" name="structured_html[template]" value="{{ old('structured_html.template', $template ?? '') }}">
<input type="hidden" id="html" name="html" value="{{ old('html', $html) }}" data-html-preview-source>
<div class="form-buttons">
    <x-mailcoach::button id="save" :label="__('Save content')"/>
    <x-mailcoach::button-secondary data-modal-trigger="edit-template" :label="__('Edit template')"/>
    <x-mailcoach::button-secondary id="preview" :label="__('Preview')"/>
    @if ($showTestButton)
        <x-mailcoach::button-secondary data-modal-trigger="send-test" :label="__('Send Test')"/>
    @endif
</div>

@push('modals')
    <x-mailcoach::modal :title="__('Edit template')" name="edit-template" large>
        <div class="p-6">
            <p class="mb-6">{!! __('Make sure to include a <code>::content::</code> placeholder where the Editor‘s content should go.') !!}</p>

            <x-mailcoach::html-field name="structured_html[template]" :value="old('structured_html.template', $template ?? '')" />

            <div class="form-buttons">
                <x-mailcoach::button data-modal-confirm="edit-template" type="button" :label=" __('Save')" />
            </div>
        </div>
    </x-mailcoach::modal>
@endpush
