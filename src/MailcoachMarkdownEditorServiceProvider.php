<?php

namespace Spatie\MailcoachMarkdownEditor;

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Mailcoach\Mailcoach;

class MailcoachMarkdownEditorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mailcoach-markdown-editor')
            ->hasViews()
            ->hasConfigFile();

        Livewire::component('mailcoach-markdown-editor::editor', Editor::class);
    }

    public function bootingPackage()
    {
        Mailcoach::editorScript(Editor::class, 'https://uicdn.toast.com/chart/latest/toastui-chart.min.js');
        Mailcoach::editorScript(Editor::class, 'https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.min.js');
        Mailcoach::editorScript(Editor::class, 'https://uicdn.toast.com/editor/3.1.5/toastui-editor-all.min.js');
        Mailcoach::editorScript(Editor::class, 'https://uicdn.toast.com/editor-plugin-code-syntax-highlight/3.0.0/toastui-editor-plugin-code-syntax-highlight-all.min.js');
        Mailcoach::editorScript(Editor::class, 'https://uicdn.toast.com/editor-plugin-table-merged-cell/3.0.1/toastui-editor-plugin-table-merged-cell.min.js');
        Mailcoach::editorScript(Editor::class, 'https://uicdn.toast.com/editor-plugin-color-syntax/3.0.3/toastui-editor-plugin-color-syntax.min.js');

        Mailcoach::editorStyle(Editor::class, 'https://uicdn.toast.com/editor/latest/toastui-editor.min.css');
        Mailcoach::editorStyle(Editor::class, 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css');
        Mailcoach::editorStyle(Editor::class, 'https://uicdn.toast.com/editor-plugin-code-syntax-highlight/3.0.0/toastui-editor-plugin-code-syntax-highlight.min.css');
        Mailcoach::editorStyle(Editor::class, 'https://uicdn.toast.com/editor-plugin-table-merged-cell/3.0.1/toastui-editor-plugin-table-merged-cell.min.css');
        Mailcoach::editorStyle(Editor::class, 'https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.min.css');
        Mailcoach::editorStyle(Editor::class, 'https://uicdn.toast.com/editor-plugin-color-syntax/latest/toastui-editor-plugin-color-syntax.min.css');
    }
}
