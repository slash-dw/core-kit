<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Enums\Concerns;

/**
 * Trait that adds custom sorting support.
 *
 * Enums using this trait automatically get sortOrder() and
 * getSortedCaseValueAndLabels(). By default, cases are sorted by their value.
 *
 * Override sortOrder() in the enum to define custom sorting.
 *
 * Example usage:
 * ```php
 * enum TimeSlot: int
 * {
 *     use BaseEnumTrait;
 *     use HasSortOrderTrait;
 *
 *     case MORNING = 1;
 *     case EVENING = 2;
 *     case AFTERNOON = 3;
 *
 *     public function label(): string { ... }
 *
 *     // Override for custom sorting.
 *     public function sortOrder(): int
 *     {
 *         return match($this) {
 *             self::MORNING => 1,
 *             self::AFTERNOON => 2,
 *             self::EVENING => 3,
 *         };
 *     }
 * }
 *
 * // Usage:
 * TimeSlot::getSortedCaseValueAndLabels();
 * // [1 => 'Morning', 3 => 'Afternoon', 2 => 'Evening'] - sorted by sortOrder()
 * ```
 */
trait HasSortOrderTrait
{
    /**
     * Returns the value used for sorting an enum case.
     *
     * By default, returns the enum value. Override this method for custom sorting.
     * Lower values come first.
     */
    public function sortOrder(): int
    {
        return $this->value;
    }

    /**
     * Returns value => label pairs sorted by each case's sortOrder() value.
     *
     * @return array<int|string, string>
     */
    public static function getSortedCaseValueAndLabels(): array
    {
        $cases = self::cases();
        usort($cases, fn ($a, $b) => $a->sortOrder() <=> $b->sortOrder());

        $result = [];
        foreach ($cases as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }
}
