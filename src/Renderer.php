<?php

namespace Spatie\MailcoachMarkdownEditor;

abstract class Renderer
{
    public function __construct(protected array $data)
    {
    }

    abstract public function render(): string;
}
