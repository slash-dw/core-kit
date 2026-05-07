<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Controllers\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use SlashDw\CoreKit\Http\Responses\ApiResponseFactory;

/**
 * Optional controller trait: delegates JSON API envelopes to {@see ApiResponseFactory}.
 */
trait ApiResponses
{
    protected function successJson(mixed $data = null, string $message = 'Success'): JsonResponse
    {
        return App::make(ApiResponseFactory::class)->success($data, $message);
    }

    /**
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     * @param  array<string, mixed>  $meta
     */
    protected function successJsonPaginated(LengthAwarePaginator $paginator, array $meta = [], string $message = 'Success'): JsonResponse
    {
        return App::make(ApiResponseFactory::class)->successPaginated($paginator, $meta, $message);
    }

    /**
     * @param  array<int, mixed>  $items
     */
    protected function successJsonForInfiniteScroll(array $items, bool $hasMore, ?int $nextOffset = null, string $message = 'Success'): JsonResponse
    {
        return App::make(ApiResponseFactory::class)->successInfiniteScroll($items, $hasMore, $nextOffset, $message);
    }

    protected function errorJson(string $message = 'Unexpected error.', int $statusCode = 400, mixed $data = null, ?string $errorCode = null): JsonResponse
    {
        return App::make(ApiResponseFactory::class)->error($message, $statusCode, $data, $errorCode);
    }

    protected function noContentJson(): JsonResponse
    {
        return App::make(ApiResponseFactory::class)->noContent();
    }
}
