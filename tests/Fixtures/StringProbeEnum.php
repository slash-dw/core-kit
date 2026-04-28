<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use SlashDw\CoreKit\Enums\Concerns\BaseEnumTrait;
use SlashDw\CoreKit\Enums\Concerns\HasColorTrait;

/** String-backed enum; HasSortOrderTrait is not used here because it expects an int value. */
enum StringProbeEnum: string
{
    use BaseEnumTrait;
    use HasColorTrait;

    case X = 'x';

    case Y = 'y';

    public function label(): string
    {
        return match ($this) {
            self::X => 'Xray',
            self::Y => 'Yank',
        };
    }
}
