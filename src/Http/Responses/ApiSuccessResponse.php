<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiSuccessResponse
{
    public function make(mixed $data, ApiMeta $meta, int $statusCode = 200): JsonResponse
    {
        return new JsonResponse([
            'data' => $data,
            'meta' => $meta->toArray(),
        ], $statusCode);
    }

    public function noContent(): JsonResponse
    {
        return new JsonResponse(null, 204);
    }
}
