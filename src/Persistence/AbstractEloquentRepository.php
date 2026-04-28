<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Persistence;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use SlashDw\CoreKit\Contracts\EloquentQueryFilterContract;

/**
 * Shared Eloquent persistence base for repositories within a module.
 *
 * This class only reduces repetitive single-record access patterns.
 * It does not include transactions, authorization, event dispatching, or cache invalidation.
 *
 * @template TModel of Model
 */
abstract class AbstractEloquentRepository
{
    /**
     * @param  TModel  $model
     */
    public function __construct(
        protected Model $model,
    ) {}

    /**
     * @return Builder<TModel>
     */
    protected function query(): Builder
    {
        /** @var Builder<TModel> $builder */
        $builder = $this->model->newQuery();

        return $builder;
    }

    /**
     * @return Builder<TModel>
     */
    public function applyFilter(EloquentQueryFilterContract $filter): Builder
    {
        $result = $filter->apply($this->query());

        if (! $result instanceof Builder) {
            throw new \LogicException('Filter::apply() must return Builder for applyFilter().');
        }

        /** @var Builder<TModel> $result */
        return $result;
    }

    /**
     * @return TModel
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(string $id): Model
    {
        $model = $this->query()->findOrFail($id);

        /** @var TModel $model */
        return $model;
    }

    /**
     * @return TModel|null
     */
    public function findById(string $id): ?Model
    {
        $model = $this->query()->find($id);

        if ($model === null) {
            return null;
        }

        /** @var TModel $model */
        return $model;
    }

    /**
     * @return TModel|null
     */
    public function findForUpdate(string $id): ?Model
    {
        $model = $this->query()
            ->whereKey($id)
            ->lockForUpdate()
            ->first();

        if ($model === null) {
            return null;
        }

        /** @var TModel $model */
        return $model;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function create(array $attributes): Model
    {
        $model = $this->model->create($attributes);

        /** @var TModel $model */
        return $model;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateById(string $id, array $attributes): bool
    {
        return $this->query()
            ->whereKey($id)
            ->firstOrFail()
            ->update($attributes);
    }

    /**
     * @param  TModel  $model
     * @param  array<string, mixed>  $attributes
     */
    public function updateByModel(Model $model, array $attributes): bool
    {
        return $model->update($attributes);
    }

    public function deleteById(string $id): bool
    {
        $deleted = $this->query()
            ->whereKey($id)
            ->firstOrFail()
            ->delete();

        return (bool) $deleted;
    }

    /**
     * @param  TModel  $model
     */
    public function deleteByModel(Model $model): bool
    {
        $deleted = $model->delete();

        return (bool) $deleted;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function insertMany(array $rows): bool
    {
        if ($rows === []) {
            return true;
        }

        return $this->query()->insert($rows);
    }
}
