<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use SlashDw\CoreKit\Http\Tracing\TraceIdResolver;

final class ApiResponseFactory
{
    public function __construct(
        private readonly TraceIdResolver $traceIdResolver,
    ) {}

    public function success(mixed $data = null, string $message = 'Success'): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $message,
            'trace_id' => $this->traceIdResolver->resolve(),
            'data' => $data,
        ]);
    }

    /**
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     * @param  array<string, mixed>  $meta
     */
    public function successPaginated(LengthAwarePaginator $paginator, array $meta = [], string $message = 'Success'): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $message,
            'trace_id' => $this->traceIdResolver->resolve(),
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more_pages' => $paginator->hasMorePages(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
            'meta' => $meta,
        ]);
    }

    /**
     * @param  array<int, mixed>  $items
     */
    public function successInfiniteScroll(array $items, bool $hasMore, ?int $nextOffset = null, string $message = 'Success'): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => $message,
            'trace_id' => $this->traceIdResolver->resolve(),
            'data' => [
                'items' => $items,
                'has_more' => $hasMore,
                'next_offset' => $nextOffset,
            ],
        ]);
    }

    public function error(string $message = 'Unexpected error.', int $statusCode = 400, mixed $data = null, ?string $errorCode = null): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $message,
            'trace_id' => $this->traceIdResolver->resolve(),
            'data' => $data,
            'error' => [
                'code' => $errorCode,
            ],
        ], $statusCode);
    }
}
