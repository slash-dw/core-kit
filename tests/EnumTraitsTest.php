<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use SlashDw\CoreKit\Tests\Fixtures\IntProbeEnum;
use SlashDw\CoreKit\Tests\Fixtures\SortDefaultIntEnum;
use SlashDw\CoreKit\Tests\Fixtures\StringProbeEnum;

final class EnumTraitsTest extends TestCase
{
    public function test_base_enum_trait_values_and_maps(): void
    {
        $this->assertSame([30, 10, 20], IntProbeEnum::getValues());
        $this->assertSame(['A', 'B', 'C'], IntProbeEnum::getNames());

        $this->assertSame([10, 20], IntProbeEnum::getValuesExceptByArrayValue([30]));
        $this->assertSame(
            [30 => 'Apple', 20 => 'Cherry'],
            IntProbeEnum::getCaseValueAndLabelsExceptByArrayValue([10]),
        );

        $this->assertSame(
            ['A' => 'Apple', 'B' => 'Banana', 'C' => 'Cherry'],
            IntProbeEnum::getNameLabelMap(),
        );

        $this->assertSame(IntProbeEnum::B, IntProbeEnum::fromLabel('Banana'));
        $this->assertNull(IntProbeEnum::fromLabel('Unknown'));

        $this->assertTrue(IntProbeEnum::B->isOneOf(IntProbeEnum::B, IntProbeEnum::C));
        $this->assertFalse(IntProbeEnum::A->isOneOf(IntProbeEnum::B, IntProbeEnum::C));
    }

    public function test_base_enum_trait_select_options_and_json(): void
    {
        $options = StringProbeEnum::getSelectOptions();
        $this->assertSame([
            ['value' => 'x', 'label' => 'Xray'],
            ['value' => 'y', 'label' => 'Yank'],
        ], $options);

        $json = StringProbeEnum::toJson();
        $this->assertSame('{"x":"Xray","y":"Yank"}', $json);
    }

    public function test_base_enum_trait_sorted_by_label_respects_direction_and_invalid_fallback(): void
    {
        $asc = IntProbeEnum::getCaseValueAndLabelsSortedByLabel(IntProbeEnum::SORT_ASC);
        $this->assertSame(
            [30 => 'Apple', 10 => 'Banana', 20 => 'Cherry'],
            $asc,
        );

        $desc = IntProbeEnum::getCaseValueAndLabelsSortedByLabel(IntProbeEnum::SORT_DESC);
        $this->assertSame(
            [20 => 'Cherry', 10 => 'Banana', 30 => 'Apple'],
            $desc,
        );

        $bogus = IntProbeEnum::getCaseValueAndLabelsSortedByLabel('invalid');
        $this->assertSame($asc, $bogus);
    }

    public function test_has_color_trait_default_and_override(): void
    {
        $this->assertSame('badge badge-light-success', IntProbeEnum::A->badgeClass());
        $this->assertSame('bg-success', IntProbeEnum::A->backgroundClass());

        $this->assertSame('badge badge-light-danger', IntProbeEnum::B->badgeClass());
        $this->assertSame('bg-danger', IntProbeEnum::B->backgroundClass());

        $this->assertSame('badge badge-light-secondary', IntProbeEnum::C->badgeClass());
        $this->assertSame('bg-secondary', IntProbeEnum::C->backgroundClass());

        $this->assertSame('secondary', StringProbeEnum::X->color());
        $this->assertSame('badge badge-light-secondary', StringProbeEnum::X->badgeClass());
    }

    public function test_has_sort_order_trait_custom_order(): void
    {
        $sorted = IntProbeEnum::getSortedCaseValueAndLabels();
        $this->assertSame(
            [20 => 'Cherry', 10 => 'Banana', 30 => 'Apple'],
            $sorted,
        );
    }

    public function test_has_sort_order_trait_defaults_to_backed_value_order(): void
    {
        $sorted = SortDefaultIntEnum::getSortedCaseValueAndLabels();
        $this->assertSame(
            [1 => 'Low', 5 => 'Mid', 10 => 'High'],
            $sorted,
        );
    }
}
