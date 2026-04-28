<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use SlashDw\CoreKit\Http\Controllers\Concerns\ApiResponses;
use SlashDw\CoreKit\Http\Controllers\Concerns\HandlesDownloadResponses;
use SlashDw\CoreKit\Http\Controllers\Concerns\HasPaginationOptions;

/**
 * Thin wrapper for calling protected trait helpers from HTTP tests.
 */
final class HttpConcernsProbeController extends Controller
{
    use ApiResponses;
    use HandlesDownloadResponses;
    use HasPaginationOptions;

    public function probeSuccessJson(): JsonResponse
    {
        return $this->successJson(['key' => 1], 'Hello');
    }

    /**
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     */
    public function probeSuccessJsonPaginated(LengthAwarePaginator $paginator): JsonResponse
    {
        return $this->successJsonPaginated($paginator, ['extra' => true], 'Paged');
    }

    public function probeSuccessJsonForInfiniteScroll(): JsonResponse
    {
        return $this->successJsonForInfiniteScroll([1, 2], true, 10, 'Inf');
    }

    public function probeErrorJson(): JsonResponse
    {
        return $this->errorJson('Nope', 409, ['detail' => 'x'], 'E_CODE');
    }

    public function probeDownloadContent(): Response
    {
        return $this->downloadContent('binary', 'file.bin', 'application/octet-stream');
    }

    /**
     * @return array<int, int>
     */
    public function probeGetPerPageOptions(): array
    {
        return self::getPerPageOptions();
    }
}
