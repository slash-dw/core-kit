<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Filter contract used by `applyFilter()` in repository layer.
 *
 * Application-side `BaseFilter` implementations (e.g. filter-kit or in-app filters)
 * implement this interface; repository usage expects a `Builder` result from `apply()`.
 */
interface EloquentQueryFilterContract
{
    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<TModel>  $query
     */
    public function apply(Builder $query): mixed;
}
