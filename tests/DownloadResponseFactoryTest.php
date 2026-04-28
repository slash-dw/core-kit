<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Foundation\Application;
use SlashDw\CoreKit\Http\Responses\DownloadResponseFactory;

final class DownloadResponseFactoryTest extends TestCase
{
    public function test_sets_body_and_attachment_headers(): void
    {
        $app = $this->requireLaravelApplication();
        $factory = $app->make(DownloadResponseFactory::class);

        $response = $factory->content("line1\nline2", 'report.csv', 'text/csv');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame("line1\nline2", $response->getContent());
        $this->assertSame('text/csv', $response->headers->get('Content-Type'));
        $this->assertSame('attachment; filename="report.csv"', $response->headers->get('Content-Disposition'));
    }

    private function requireLaravelApplication(): Application
    {
        $app = $this->app;
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }
}
