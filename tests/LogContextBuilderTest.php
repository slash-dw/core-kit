<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use SlashDw\CoreKit\Logging\LogContextBuilder;

final class LogContextBuilderTest extends TestCase
{
    public function test_build_with_only_extra(): void
    {
        $app = $this->createMock(Application::class);
        $app->method('bound')->with('request')->willReturn(false);

        $builder = new LogContextBuilder($app);
        $context = $builder->build(['a' => 1]);

        $this->assertSame(['a' => 1], $context['extra']);
        $this->assertArrayNotHasKey('actor', $context);
        $this->assertArrayNotHasKey('request', $context);
    }

    public function test_build_includes_actor_when_authenticated(): void
    {
        $this->actingAs(new GenericUser(['id' => 42]), 'web');

        $builder = $this->laravel()->make(LogContextBuilder::class);
        $context = $builder->build([], 'web');

        $this->assertSame(['guard' => 'web', 'id' => 42], $context['actor']);
    }

    public function test_build_includes_actor_with_null_id_when_guest(): void
    {
        $builder = $this->laravel()->make(LogContextBuilder::class);
        $context = $builder->build([], 'web');

        $this->assertSame(['guard' => 'web', 'id' => null], $context['actor']);
    }

    public function test_empty_auth_guard_skips_actor(): void
    {
        $app = $this->createMock(Application::class);
        $app->method('bound')->with('request')->willReturn(false);

        $builder = new LogContextBuilder($app);

        $this->assertSame(['extra' => ['x' => true]], $builder->build(['x' => true], ''));
        $this->assertSame(['extra' => ['x' => true]], $builder->build(['x' => true], null));
    }

    public function test_build_includes_request_slice_with_trace_header(): void
    {
        $this->laravel()->instance('request', Request::create(
            'https://example.test/items',
            'POST',
            [],
            [],
            [],
            ['HTTP_X_TRACE_ID' => 'tid-abc'],
        ));

        $builder = $this->laravel()->make(LogContextBuilder::class);
        $context = $builder->build();

        $this->assertArrayHasKey('request', $context);
        $this->assertSame('POST', $context['request']['method']);
        $this->assertSame('https://example.test/items', $context['request']['url']);
        $this->assertSame('tid-abc', $context['request']['trace_id']);
    }

    public function test_build_prefers_x_request_id_when_x_trace_id_absent(): void
    {
        $this->laravel()->instance('request', Request::create(
            'https://example.test/r',
            'GET',
            [],
            [],
            [],
            ['HTTP_X_REQUEST_ID' => 'rid-1'],
        ));

        $builder = $this->laravel()->make(LogContextBuilder::class);
        $context = $builder->build();

        $this->assertSame('rid-1', $context['request']['trace_id']);
    }

    public function test_non_request_binding_yields_no_request_context(): void
    {
        $app = $this->createMock(Application::class);
        $app->method('bound')->with('request')->willReturn(true);
        $app->method('make')->with('request')->willReturn(new \stdClass);

        $builder = new LogContextBuilder($app);

        $this->assertArrayNotHasKey('request', $builder->build());
    }
}
