<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Enums\Concerns;

/**
 * Base helper methods for PHP native enums.
 *
 * ## Basic Usage
 * All enums using this trait should implement the label() method.
 *
 * ## Additional Traits
 * - HasColorTrait: UI color support (color, badgeClass, backgroundClass)
 * - HasSortOrderTrait: Custom sort support (sortOrder, getSortedCaseValueAndLabels)
 *
 * @see HasColorTrait
 * @see HasSortOrderTrait
 */
trait BaseEnumTrait
{
    public const SORT_ASC = 'ASC';

    public const SORT_DESC = 'DESC';

    /**
     * Returns only enum backing values.
     *
     * @return array<int|string>
     */
    public static function getValues(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Returns values except the provided enum values.
     *
     * @param  array<int|string>  $exceptedValues
     * @return array<int|string>
     */
    public static function getValuesExceptByArrayValue(array $exceptedValues): array
    {
        return array_values(array_filter(
            self::getValues(),
            fn ($value) => ! in_array($value, $exceptedValues, true)
        ));
    }

    /**
     * Returns enum value => label map.
     *
     * @return array<int|string, string>
     */
    public static function getCaseValueAndLabels(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->label();
        }

        return $map;
    }

    /**
     * Returns value => label map excluding specific enum values.
     *
     * @param  array<int|string>  $exceptedValues
     * @return array<int|string, string>
     */
    public static function getCaseValueAndLabelsExceptByArrayValue(array $exceptedValues): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            if (! in_array($case->value, $exceptedValues, true)) {
                $map[$case->value] = $case->label();
            }
        }

        return $map;
    }

    /**
     * Returns value => label JSON output (suitable for Vue/JS).
     */
    public static function toJson(): string
    {
        $json = json_encode(self::getCaseValueAndLabels(), JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            return '{}';
        }

        return $json;
    }

    /**
     * Returns the enum case that matches the given label.
     */
    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Returns ['value' => x, 'label' => y] options for select forms.
     *
     * @return list<array{value: int|string, label: string}>
     */
    public static function getSelectOptions(): array
    {
        return array_map(fn (self $case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }

    /**
     * Checks whether this enum case is in the given group.
     */
    public function isOneOf(self ...$cases): bool
    {
        return in_array($this, $cases, true);
    }

    /**
     * List of enum case names.
     *
     * @return array<int, string>
     */
    public static function getNames(): array
    {
        return array_map(fn (self $case) => $case->name, self::cases());
    }

    /**
     * Returns name => label map.
     *
     * @return array<string, string>
     */
    public static function getNameLabelMap(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->name] = $case->label();
        }

        return $map;
    }

    /**
     * Sorts enum value => label map by label value.
     *
     * @param  string  $direction  self::SORT_ASC or self::SORT_DESC
     * @return array<int|string, string>
     */
    public static function getCaseValueAndLabelsSortedByLabel(string $direction = self::SORT_ASC): array
    {
        $map = self::getCaseValueAndLabels();

        $direction = strtoupper($direction);
        if (! in_array($direction, [self::SORT_ASC, self::SORT_DESC], true)) {
            $direction = self::SORT_ASC;
        }

        if ($direction === self::SORT_ASC) {
            asort($map, SORT_NATURAL | SORT_FLAG_CASE);
        } else {
            arsort($map, SORT_NATURAL | SORT_FLAG_CASE);
        }

        return $map;
    }

    /**
     * label() must be implemented on each enum case.
     */
    abstract public function label(): string;
}
