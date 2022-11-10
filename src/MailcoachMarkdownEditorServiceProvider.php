<?php

namespace Spatie\MailcoachMarkdownEditor;

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailcoachMarkdownEditorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mailcoach-markdown-editor')
            ->hasViews()
            ->hasAssets()
            ->hasConfigFile()
            ->hasMigration('create_mailcoach_markdown_editor_tables');
    }

    public function bootingPackage()
    {
        Route::macro('mailcoachMarkdownEditor', function (string $url = '') {
            Route::prefix($url)->group(function () {
                $middlewareClasses = config('mailcoach.middleware.web', []);

                Route::middleware($middlewareClasses)->prefix('')->group(__DIR__ . '/../routes/api.php');
            });
        });
    }
}
