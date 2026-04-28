<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use SlashDw\CoreKit\Contracts\EloquentQueryFilterContract;

/** Returns a non-Builder from applyFilter() to exercise the LogicException scenario. */
final class WidgetBadFilter implements EloquentQueryFilterContract
{
    public function apply(Builder $query): mixed
    {
        return 'not-a-builder';
    }
}
