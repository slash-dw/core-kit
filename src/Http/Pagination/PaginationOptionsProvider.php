<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Pagination;

use Illuminate\Contracts\Config\Repository;

final class PaginationOptionsProvider
{
    public function __construct(
        private readonly Repository $config,
    ) {}

    /**
     * @return array<int, int>
     */
    public function perPageOptions(): array
    {
        $options = $this->config->get('core_kit.pagination.per_page_options', [30, 50, 75, 100]);

        if (! is_array($options)) {
            return [30, 50, 75, 100];
        }

        $normalized = [];

        foreach ($options as $option) {
            if (is_int($option) && $option > 0) {
                $normalized[] = $option;
            }
        }

        if ($normalized === []) {
            return [30, 50, 75, 100];
        }

        $unique = array_values(array_unique($normalized));
        sort($unique);

        return $unique;
    }
}
