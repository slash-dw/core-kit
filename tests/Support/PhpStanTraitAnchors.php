<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Support;

use Illuminate\Routing\Controller;
use SlashDw\CoreKit\Enums\Concerns\BaseEnumTrait;
use SlashDw\CoreKit\Enums\Concerns\HasColorTrait;
use SlashDw\CoreKit\Enums\Concerns\HasSortOrderTrait;
use SlashDw\CoreKit\Http\Controllers\Concerns\ApiResponses;
use SlashDw\CoreKit\Http\Controllers\Concerns\HandlesDownloadResponses;
use SlashDw\CoreKit\Http\Controllers\Concerns\HasPaginationOptions;

/**
 * This file only exists so PHPStan analyzes the traits; it is not production behavior.
 * {@see HasSortOrderTrait} uses the default `sortOrder()` for an `int` backed enum.
 */
enum PhpStanDummyIntEnum: int
{
    use BaseEnumTrait;
    use HasColorTrait;
    use HasSortOrderTrait;

    case Alpha = 1;

    public function label(): string
    {
        return 'Alpha';
    }
}

final class PhpStanDummyController extends Controller
{
    use ApiResponses;
    use HandlesDownloadResponses;
    use HasPaginationOptions;
}
