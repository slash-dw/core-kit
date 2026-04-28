<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use SlashDw\CoreKit\Enums\Concerns\BaseEnumTrait;
use SlashDw\CoreKit\Enums\Concerns\HasColorTrait;
use SlashDw\CoreKit\Enums\Concerns\HasSortOrderTrait;

/**
 * Uses three traits together with custom color() values and sortOrder() sequence (C -> B -> A).
 */
enum IntProbeEnum: int
{
    use BaseEnumTrait;
    use HasColorTrait;
    use HasSortOrderTrait;

    case A = 30;

    case B = 10;

    case C = 20;

    public function label(): string
    {
        return match ($this) {
            self::A => 'Apple',
            self::B => 'Banana',
            self::C => 'Cherry',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::A => 'success',
            self::B => 'danger',
            default => 'secondary',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            self::C => 1,
            self::B => 2,
            self::A => 3,
        };
    }
}
