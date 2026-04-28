<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use SlashDw\CoreKit\Http\Responses\ApiResponseFactory;

final class ApiResponseFactoryTest extends TestCase
{
    public function test_success_envelope_and_trace_id(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', 'trace-success');

        $factory = $app->make(ApiResponseFactory::class);
        $response = $factory->success(['item' => 1], 'Done');

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertTrue($payload['success']);
        $this->assertSame('Done', $payload['message']);
        $this->assertSame('trace-success', $payload['trace_id']);
        $this->assertSame(['item' => 1], $payload['data']);
    }

    public function test_success_paginated_structure(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', 'trace-page');

        $items = [['id' => 1], ['id' => 2]];
        $paginator = new LengthAwarePaginator($items, 20, 2, 2);
        $paginator->setPath('https://example.test/items');

        $factory = $app->make(ApiResponseFactory::class);
        $response = $factory->successPaginated($paginator, ['version' => 3], 'Listed');

        $this->assertSame(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertTrue($payload['success']);
        $this->assertSame('Listed', $payload['message']);
        $this->assertSame('trace-page', $payload['trace_id']);
        $this->assertSame($items, $payload['data']);
        $this->assertSame(2, $payload['pagination']['current_page']);
        $this->assertSame(10, $payload['pagination']['last_page']);
        $this->assertSame(2, $payload['pagination']['per_page']);
        $this->assertSame(20, $payload['pagination']['total']);
        $this->assertTrue($payload['pagination']['has_more_pages']);
        $this->assertSame(['version' => 3], $payload['meta']);
    }

    public function test_success_infinite_scroll_shape(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', 'trace-inf');

        $factory = $app->make(ApiResponseFactory::class);
        $response = $factory->successInfiniteScroll([10, 20], false, null, 'More');

        $payload = $response->getData(true);
        $this->assertSame([10, 20], $payload['data']['items']);
        $this->assertFalse($payload['data']['has_more']);
        $this->assertNull($payload['data']['next_offset']);
        $this->assertSame('trace-inf', $payload['trace_id']);
    }

    public function test_error_sets_status_and_error_code(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', 'trace-err');

        $factory = $app->make(ApiResponseFactory::class);
        $response = $factory->error('Bad input', 422, ['field' => 'email'], 'E_VALIDATION');

        $this->assertSame(422, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertFalse($payload['success']);
        $this->assertSame('Bad input', $payload['message']);
        $this->assertSame('trace-err', $payload['trace_id']);
        $this->assertSame(['field' => 'email'], $payload['data']);
        $this->assertSame('E_VALIDATION', $payload['error']['code']);
    }

    private function requireLaravelApplication(): Application
    {
        $app = $this->app;
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }
}
