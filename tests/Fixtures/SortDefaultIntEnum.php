<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use SlashDw\CoreKit\Enums\Concerns\BaseEnumTrait;
use SlashDw\CoreKit\Enums\Concerns\HasSortOrderTrait;

/** No sortOrder() override; sorting follows the enum value order. */
enum SortDefaultIntEnum: int
{
    use BaseEnumTrait;
    use HasSortOrderTrait;

    case Low = 1;

    case Mid = 5;

    case High = 10;

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Mid => 'Mid',
            self::High => 'High',
        };
    }
}
