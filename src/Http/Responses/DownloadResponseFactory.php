<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

use Illuminate\Http\Response;

final class DownloadResponseFactory
{
    public function content(string $content, string $filename, string $mimeType): Response
    {
        return new Response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ]);
    }
}
