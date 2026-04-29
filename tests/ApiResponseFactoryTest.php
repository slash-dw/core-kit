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
        $this->assertSame(['item' => 1], $payload['data']);
        $this->assertSame('trace-success', $payload['meta']['trace_id']);
        $this->assertSame('Done', $payload['meta']['message']);
        $this->assertArrayHasKey('timestamp', $payload['meta']);
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
        $this->assertSame($items, $payload['data']);
        $this->assertSame('trace-page', $payload['meta']['trace_id']);
        $this->assertSame(2, $payload['meta']['pagination']['page']);
        $this->assertSame(10, $payload['meta']['pagination']['total_pages']);
        $this->assertSame(2, $payload['meta']['pagination']['per_page']);
        $this->assertSame(20, $payload['meta']['pagination']['total']);
        $this->assertTrue($payload['meta']['pagination']['has_next']);
        $this->assertTrue($payload['meta']['pagination']['has_prev']);
        $this->assertSame(3, $payload['meta']['version']);
        $this->assertSame('Listed', $payload['meta']['message']);
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
        $this->assertSame('trace-inf', $payload['meta']['trace_id']);
        $this->assertSame('More', $payload['meta']['message']);
    }

    public function test_error_sets_status_and_error_code(): void
    {
        $app = $this->requireLaravelApplication();
        $app->instance('requestId', 'trace-err');

        $factory = $app->make(ApiResponseFactory::class);
        $response = $factory->error('Bad input', 422, ['field' => 'email'], 'E_VALIDATION');

        $this->assertSame(422, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('trace-err', $payload['meta']['trace_id']);
        $this->assertSame(422, $payload['errors'][0]['status']);
        $this->assertSame('E_VALIDATION', $payload['errors'][0]['code']);
        $this->assertSame('Bad input', $payload['errors'][0]['detail']);
        $this->assertSame(['field' => 'email'], $payload['errors'][0]['source']);
    }

    private function requireLaravelApplication(): Application
    {
        $app = $this->app;
        $this->assertInstanceOf(Application::class, $app);

        return $app;
    }
}
