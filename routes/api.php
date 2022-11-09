<?php

use Spatie\MailcoachMarkdownEditor\Http\Controllers\EditorController;

Route::post('upload', ['\\' . EditorController::class, 'upload']);

