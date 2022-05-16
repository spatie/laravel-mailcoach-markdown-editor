<?php

return [
    /**
     * The Toast UI Editor options you want to override, for a list
     * of options, check out the options parameters object here
     * @see https://nhn.github.io/tui.editor/latest/ToastUIEditorCore
     */
    'options' => [
        'initialEditType' => 'markdown', // 'markdown' or 'wysiwyg'
        'previewStyle' => 'vertical', // 'vertical' or 'tab'
        'height' => '600px',
        'placeholder' => 'Start writing...',
    ],

    /*
     * The disk on which to store uploaded images from the editor. Choose
     * one or more of the disks you've configured in config/filesystems.php.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * The media collection name to use when storing uploaded images from the editor.
     * You probably don't need to change this,
     * unless you're already using spatie/laravel-medialibrary in your project.
     */
     'collection_name' => env('MEDIA_COLLECTION', 'default'),
];
