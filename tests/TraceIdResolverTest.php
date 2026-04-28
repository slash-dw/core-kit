<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use SlashDw\CoreKit\Http\Tracing\TraceIdResolver;

final class TraceIdResolverTest extends TestCase
{
    public function test_returns_request_id_when_bound_and_non_empty(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', 'rid-from-binding');

        $resolver = $app->make(TraceIdResolver::class);

        $this->assertSame('rid-from-binding', $resolver->resolve());
    }

    public function test_empty_request_id_falls_through_to_request_headers(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', '');
        $app->instance('request', Request::create('/test', 'GET', [], [], [], [
            'HTTP_X_TRACE_ID' => 'from-header',
        ]));

        $resolver = $app->make(TraceIdResolver::class);

        $this->assertSame('from-header', $resolver->resolve());
    }

    public function test_returns_empty_string_when_request_not_bound(): void
    {
        $app = $this->createMock(Application::class);
        $app->method('bound')->willReturnMap([
            ['requestId', false],
            ['request', false],
        ]);
        $app->expects($this->never())->method('make');

        $config = $this->createStub(Repository::class);

        $resolver = new TraceIdResolver($app, $config);

        $this->assertSame('', $resolver->resolve());
    }

    public function test_non_request_binding_returns_empty_string(): void
    {
        $app = $this->createMock(Application::class);
        $app->method('bound')->willReturnMap([
            ['requestId', false],
            ['request', true],
        ]);
        $app->expects($this->once())
            ->method('make')
            ->with('request')
            ->willReturn(new \stdClass);

        $config = $this->createStub(Repository::class);

        $resolver = new TraceIdResolver($app, $config);

        $this->assertSame('', $resolver->resolve());
    }

    public function test_first_configured_header_with_value_wins(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.tracing.headers', ['X-First', 'X-Second']);
        $app->instance('request', Request::create('/test', 'GET', [], [], [], [
            'HTTP_X_FIRST' => '',
            'HTTP_X_SECOND' => 'second-wins',
        ]));

        $resolver = $app->make(TraceIdResolver::class);

        $this->assertSame('second-wins', $resolver->resolve());
    }

    public function test_skips_invalid_header_names_in_config_list(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.tracing.headers', ['', 99, 'X-Valid']);
        $app->instance('request', Request::create('/test', 'GET', [], [], [], [
            'HTTP_X_VALID' => 'ok',
        ]));

        $resolver = $app->make(TraceIdResolver::class);

        $this->assertSame('ok', $resolver->resolve());
    }

    public function test_non_array_headers_config_uses_fallback_header_only(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.tracing.headers', 'not-an-array');
        $app['config']->set('core_kit.tracing.fallback_header', 'X-Fallback');
        $app->instance('request', Request::create('/test', 'GET', [], [], [], [
            'HTTP_X_FALLBACK' => 'fb-value',
        ]));

        $resolver = $app->make(TraceIdResolver::class);

        $this->assertSame('fb-value', $resolver->resolve());
    }

    public function test_uses_fallback_header_when_primary_headers_miss(): void
    {
        $app = $this->requireLaravelApplication();
        $app['config']->set('core_kit.tracing.headers', ['X-Missing']);
        $app['config']->set('core_kit.tracing.fallback_header', 'X-Fallback');
        $app->instance('request', Request::create('/test', 'GET', [], [], [], [
            'HTTP_X_FALLBACK' => 'via-fallback',
        ]));

        $resolver = $app->make(TraceIdResolver::class);

        $this->assertSame('via-fallback', $resolver->resolve());
    }

    private function requireLaravelApplication(): \Illuminate\Foundation\Application
    {
        $app = $this->app;
        $this->assertInstanceOf(\Illuminate\Foundation\Application::class, $app);

        return $app;
    }
}
