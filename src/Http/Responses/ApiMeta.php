<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

/**
 * Canonical meta envelope for API success/error responses.
 *
 * @phpstan-type ApiPagination array{
 *   page:int,
 *   per_page:int,
 *   total:int,
 *   total_pages:int,
 *   has_next:bool,
 *   has_prev:bool
 * }
 */
final readonly class ApiMeta
{
    /**
     * @param  ApiPagination|null  $pagination
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public string $traceId,
        public string $timestamp,
        public ?array $pagination = null,
        public array $extra = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $meta = [
            'trace_id' => $this->traceId,
            'timestamp' => $this->timestamp,
        ];

        if ($this->pagination !== null) {
            $meta['pagination'] = $this->pagination;
        }

        foreach ($this->extra as $key => $value) {
            $meta[$key] = $value;
        }

        return $meta;
    }
}
