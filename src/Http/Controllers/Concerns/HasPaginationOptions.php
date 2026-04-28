<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Http\Controllers\Concerns;

use Illuminate\Support\Facades\App;
use SlashDw\CoreKit\Http\Pagination\PaginationOptionsProvider;

/**
 * Optional controller trait: exposes per-page options from {@see PaginationOptionsProvider} (values come from the consuming app's core_kit config).
 */
trait HasPaginationOptions
{
    /**
     * @return array<int, int>
     */
    public static function getPerPageOptions(): array
    {
        return App::make(PaginationOptionsProvider::class)->perPageOptions();
    }
}
