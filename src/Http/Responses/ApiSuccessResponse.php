<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class ApiSuccessResponse
{
    public function make(mixed $data, ApiMeta $meta, int $statusCode = 200): JsonResponse
    {
        return new JsonResponse([
            'data' => $data,
            'meta' => $meta->toArray(),
        ], $statusCode);
    }

    public function noContent(): Response
    {
        return response('', 204);
    }
}
