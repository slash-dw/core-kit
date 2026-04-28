<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Foundation\Application;
use SlashDw\CoreKit\Cache\CacheInvalidator;
use SlashDw\CoreKit\Cache\TaggedCacheInvalidator;
use SlashDw\CoreKit\Http\Pagination\PaginationOptionsProvider;
use SlashDw\CoreKit\Http\Responses\ApiResponseFactory;
use SlashDw\CoreKit\Http\Responses\DownloadResponseFactory;
use SlashDw\CoreKit\Http\Tracing\TraceIdResolver;

final class CoreKitBindingsTest extends TestCase
{
    public function test_container_resolves_http_and_tracing_services(): void
    {
        $app = $this->requireLaravelApplication();
        $this->assertInstanceOf(ApiResponseFactory::class, $app->make(ApiResponseFactory::class));
        $this->assertInstanceOf(DownloadResponseFactory::class, $app->make(DownloadResponseFactory::class));
        $this->assertInstanceOf(TraceIdResolver::class, $app->make(TraceIdResolver::class));
        $this->assertInstanceOf(PaginationOptionsProvider::class, $app->make(PaginationOptionsProvider::class));
        $this->assertInstanceOf(TaggedCacheInvalidator::class, $app->make(CacheInvalidator::class));
    }

    public function test_pagination_options_provider_reads_config(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.pagination.per_page_options', [11, 22]);

        $options = $app->make(PaginationOptionsProvider::class)->perPageOptions();

        $this->assertSame([11, 22], $options);
    }

    private function requireLaravelApplication(): Application
    {
        $app = $this->app;
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }
}
