<?php

declare(strict_types=1);

namespace SlashDw\CoreKit;

use Illuminate\Support\ServiceProvider;
use SlashDw\CoreKit\Cache\CacheInvalidator;
use SlashDw\CoreKit\Cache\TaggedCacheInvalidator;
use SlashDw\CoreKit\Http\Pagination\PaginationOptionsProvider;
use SlashDw\CoreKit\Http\Responses\ApiResponseFactory;
use SlashDw\CoreKit\Http\Responses\DownloadResponseFactory;
use SlashDw\CoreKit\Http\Tracing\TraceIdResolver;
use SlashDw\CoreKit\Logging\ExceptionLogger;
use SlashDw\CoreKit\Logging\LogContextBuilder;

class CoreKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/core_kit.php', 'core_kit');

        $this->app->bind(CacheInvalidator::class, TaggedCacheInvalidator::class);

        $this->app->singleton(LogContextBuilder::class, function ($app): LogContextBuilder {
            return new LogContextBuilder($app);
        });
        $this->app->singleton(ExceptionLogger::class, function ($app): ExceptionLogger {
            return new ExceptionLogger($app->make(LogContextBuilder::class));
        });

        $this->app->singleton(TraceIdResolver::class);
        $this->app->singleton(PaginationOptionsProvider::class);
        $this->app->singleton(DownloadResponseFactory::class);
        $this->app->singleton(ApiResponseFactory::class, function ($app): ApiResponseFactory {
            return new ApiResponseFactory($app->make(TraceIdResolver::class));
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/core_kit.php' => config_path('core_kit.php'),
        ], 'core-kit-config');
    }
}
