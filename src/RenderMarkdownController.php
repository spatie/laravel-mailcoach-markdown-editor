<?php

namespace Spatie\MailcoachMarkdownEditor;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction;

class RenderMarkdownController
{
    public function __invoke(Request $request, RenderMarkdownToHtmlAction $action): string
    {
        $data = $request->validate([
            'markdown' => ['nullable', 'string'],
            'theme' => ['nullable'],
        ]);

        if (empty($data['markdown'])) {
            return '';
        }

        return $action->execute($data['markdown'], $data['theme'])->toHtml();
    }
}
