<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Responses;

final readonly class ApiErrorItem
{
    /**
     * @param  array<string, mixed>|null  $source
     */
    public function __construct(
        public int $status,
        public string $code,
        public string $title,
        public string $detail,
        public ?array $source = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'status' => $this->status,
            'code' => $this->code,
            'title' => $this->title,
            'detail' => $this->detail,
        ];

        if ($this->source !== null) {
            $payload['source'] = $this->source;
        }

        return $payload;
    }
}
