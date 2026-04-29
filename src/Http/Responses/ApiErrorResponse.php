<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiErrorResponse
{
    /**
     * @param  list<ApiErrorItem>  $errors
     */
    public function make(array $errors, ApiMeta $meta, int $statusCode): JsonResponse
    {
        return new JsonResponse([
            'errors' => array_map(
                static fn (ApiErrorItem $error): array => $error->toArray(),
                $errors,
            ),
            'meta' => $meta->toArray(),
        ], $statusCode);
    }
}
