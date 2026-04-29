<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use SlashDw\CoreKit\Http\Tracing\TraceIdResolver;

final class ApiResponseFactory
{
    public function __construct(
        private readonly TraceIdResolver $traceIdResolver,
        private readonly ApiSuccessResponse $apiSuccessResponse,
        private readonly ApiErrorResponse $apiErrorResponse,
    ) {}

    public function success(mixed $data = null, string $message = 'Success'): JsonResponse
    {
        return $this->apiSuccessResponse->make(
            $data,
            $this->meta($message),
            200,
        );
    }

    /**
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     * @param  array<string, mixed>  $meta
     */
    public function successPaginated(LengthAwarePaginator $paginator, array $meta = [], string $message = 'Success'): JsonResponse
    {
        $pagination = [
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'total_pages' => $paginator->lastPage(),
            'has_next' => $paginator->hasMorePages(),
            'has_prev' => $paginator->currentPage() > 1,
        ];

        return $this->apiSuccessResponse->make(
            $paginator->items(),
            $this->meta($message, $pagination, $meta),
            200,
        );
    }

    /**
     * @param  array<int, mixed>  $items
     */
    public function successInfiniteScroll(array $items, bool $hasMore, ?int $nextOffset = null, string $message = 'Success'): JsonResponse
    {
        return $this->apiSuccessResponse->make(
            [
                'items' => $items,
                'has_more' => $hasMore,
                'next_offset' => $nextOffset,
            ],
            $this->meta($message),
            200,
        );
    }

    public function error(string $message = 'Unexpected error.', int $statusCode = 400, mixed $data = null, ?string $errorCode = null): JsonResponse
    {
        $source = null;
        if (is_array($data)) {
            $source = $data;
        }

        $error = new ApiErrorItem(
            status: $statusCode,
            code: $errorCode ?? 'bad_request',
            title: 'Request failed',
            detail: $message,
            source: $source,
        );

        return $this->apiErrorResponse->make([$error], $this->meta(), $statusCode);
    }

    public function noContent(): Response
    {
        return $this->apiSuccessResponse->noContent();
    }

    /**
     * @param  array{
     *   page:int,
     *   per_page:int,
     *   total:int,
     *   total_pages:int,
     *   has_next:bool,
     *   has_prev:bool
     * }|null  $pagination
     * @param  array<string, mixed>  $extra
     */
    private function meta(string $message = 'Success', ?array $pagination = null, array $extra = []): ApiMeta
    {
        $metaExtra = $extra;
        if ($message !== '' && $message !== 'Success') {
            $metaExtra['message'] = $message;
        }

        return new ApiMeta(
            traceId: $this->traceIdResolver->resolve(),
            timestamp: now()->utc()->format(DATE_ATOM),
            pagination: $pagination,
            extra: $metaExtra,
        );
    }
}
