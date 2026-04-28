<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Controllers\Concerns;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use SlashDw\CoreKit\Http\Responses\DownloadResponseFactory;

/**
 * Optional controller trait: delegates download responses to {@see DownloadResponseFactory}.
 */
trait HandlesDownloadResponses
{
    protected function downloadContent(string $content, string $filename, string $mimeType): Response
    {
        return App::make(DownloadResponseFactory::class)->content($content, $filename, $mimeType);
    }
}
