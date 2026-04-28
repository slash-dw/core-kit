<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Enums\Concerns;

/**
 * Trait that adds UI color support.
 *
 * Enums using this trait get color(), badgeClass(), and backgroundClass() methods.
 * Default color is returned as 'secondary'.
 *
 * Override color() in enum for custom color mappings.
 *
 * Example:
 * ```php
 * enum Status: int
 * {
 *     use BaseEnumTrait;
 *     use HasColorTrait;
 *
 *     case PENDING = 1;
 *     case APPROVED = 2;
 *
 *     public function label(): string { ... }
 *
 *     public function color(): string
 *     {
 *         return match($this) {
 *             self::PENDING => 'warning',
 *             self::APPROVED => 'success',
 *         };
 *     }
 * }
 *
 * $status->color();           // 'warning'
 * $status->badgeClass();      // 'badge badge-light-warning'
 * $status->backgroundClass(); // 'bg-warning'
 * ```
 */
trait HasColorTrait
{
    /**
     * Returns color value for enum case.
     *
     * Defaults to 'secondary'. Override for custom color mappings.
     *
     * Bootstrap color names: primary, secondary, success, danger, warning, info, light, dark
     */
    public function color(): string
    {
        return 'secondary';
    }

    /**
     * Returns Bootstrap badge class.
     *
     * @return string Example: 'badge badge-light-warning'
     */
    public function badgeClass(): string
    {
        return 'badge badge-light-'.$this->color();
    }

    /**
     * Returns Bootstrap background class.
     *
     * @return string Example: 'bg-warning'
     */
    public function backgroundClass(): string
    {
        return 'bg-'.$this->color();
    }
}
