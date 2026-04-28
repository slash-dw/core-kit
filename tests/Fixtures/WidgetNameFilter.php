<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use SlashDw\CoreKit\Contracts\EloquentQueryFilterContract;

final class WidgetNameFilter implements EloquentQueryFilterContract
{
    public function __construct(
        private readonly string $name,
    ) {}

    public function apply(Builder $query): mixed
    {
        return $query->where('name', $this->name);
    }
}
