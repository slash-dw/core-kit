<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use SlashDw\CoreKit\Tests\Fixtures\HttpConcernsProbeController;

final class HttpControllerConcernsTest extends TestCase
{
    public function test_api_responses_trait_delegates_to_factory(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', 'trace-trait');

        $controller = new HttpConcernsProbeController;

        $success = $controller->probeSuccessJson();
        $this->assertSame(200, $success->getStatusCode());
        $body = $success->getData(true);
        $this->assertSame(['key' => 1], $body['data']);
        $this->assertSame('Hello', $body['meta']['message']);
        $this->assertSame('trace-trait', $body['meta']['trace_id']);

        $items = [['a' => 1]];
        $paginator = new LengthAwarePaginator($items, 5, 1, 1);
        $paginator->setPath('https://example.test/p');
        $paged = $controller->probeSuccessJsonPaginated($paginator);
        $this->assertSame($items, $paged->getData(true)['data']);
        $this->assertSame(true, $paged->getData(true)['meta']['extra']);

        $inf = $controller->probeSuccessJsonForInfiniteScroll();
        $this->assertTrue($inf->getData(true)['data']['has_more']);

        $err = $controller->probeErrorJson();
        $this->assertSame(409, $err->getStatusCode());
        $this->assertSame('E_CODE', $err->getData(true)['errors'][0]['code']);
    }

    public function test_handles_download_responses_trait(): void
    {
        $controller = new HttpConcernsProbeController;
        $download = $controller->probeDownloadContent();

        $this->assertSame('binary', $download->getContent());
        $this->assertStringContainsString('file.bin', (string) $download->headers->get('Content-Disposition'));
        $this->assertSame('application/octet-stream', $download->headers->get('Content-Type'));
    }

    public function test_has_pagination_options_trait_reads_config(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.pagination.per_page_options', [7, 14, 7]);

        $controller = new HttpConcernsProbeController;
        $this->assertSame([7, 14], $controller->probeGetPerPageOptions());
    }

    private function requireLaravelApplication(): Application
    {
        $app = $this->app;
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }
}
