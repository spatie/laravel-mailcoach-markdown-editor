<?php

namespace Spatie\MailcoachMarkdownEditor;

use Illuminate\Contracts\View\View;
use Spatie\Mailcoach\Domain\Campaign\Livewire\EditorComponent;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;

class Editor extends EditorComponent
{
    public bool $supportsTemplates = false;

    public function render(): View
    {
        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                if (! is_array($this->templateFieldValues[$placeHolderName] ?? '')) {
                    $this->templateFieldValues[$placeHolderName] = [];
                }

                $this->templateFieldValues[$placeHolderName]['html'] ??= '';
                $this->templateFieldValues[$placeHolderName]['markdown'] ??= '';
            }
        } else {
            if (! is_array($this->templateFieldValues['html'])) {
                $this->templateFieldValues['html'] = [];
            }

            $this->templateFieldValues['html']['html'] ??= '';
            $this->templateFieldValues['html']['markdown'] ??= '';
        }

        return view('mailcoach-markdown-editor::editor');
    }

    public function renderFullHtml()
    {
        $templateRenderer = (new TemplateRenderer($this->template?->html ?? ''));
        $this->fullHtml = $templateRenderer->render(collect($this->templateFieldValues)->map(function ($values) {
            if (is_string($values)) {
                return $values;
            }

            return $values['html'];
        })->toArray());
    }
}
