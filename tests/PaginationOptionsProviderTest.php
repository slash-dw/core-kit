<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Foundation\Application;
use SlashDw\CoreKit\Http\Pagination\PaginationOptionsProvider;

final class PaginationOptionsProviderTest extends TestCase
{
    public function test_non_array_config_returns_defaults(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.pagination.per_page_options', 'invalid');

        $options = $app->make(PaginationOptionsProvider::class)->perPageOptions();

        $this->assertSame([30, 50, 75, 100], $options);
    }

    public function test_filters_non_positive_and_non_integers(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.pagination.per_page_options', [10, 0, -3, '20', 10.5, 25]);

        $options = $app->make(PaginationOptionsProvider::class)->perPageOptions();

        $this->assertSame([10, 25], $options);
    }

    public function test_empty_after_normalization_returns_defaults(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.pagination.per_page_options', [0, 'x', -1]);

        $options = $app->make(PaginationOptionsProvider::class)->perPageOptions();

        $this->assertSame([30, 50, 75, 100], $options);
    }

    public function test_deduplicates_and_sorts(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.pagination.per_page_options', [50, 10, 50, 30, 10]);

        $options = $app->make(PaginationOptionsProvider::class)->perPageOptions();

        $this->assertSame([10, 30, 50], $options);
    }

    private function requireLaravelApplication(): Application
    {
        $app = $this->app;
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }
}
