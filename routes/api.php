<?php

Route::post('render-markdown', '\\'.\Spatie\MailcoachMarkdownEditor\RenderMarkdownController::class)
    ->name('mailcoach-markdown-editor.render-markdown');
