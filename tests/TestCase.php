<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use SlashDw\CoreKit\CoreKitServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            CoreKitServiceProvider::class,
        ];
    }

    protected function laravel(): Application
    {
        $app = $this->app;
        self::assertInstanceOf(Application::class, $app);

        return $app;
    }
}
