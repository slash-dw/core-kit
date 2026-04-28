<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use SlashDw\CoreKit\Persistence\AbstractEloquentRepository;

/**
 * @extends AbstractEloquentRepository<RepoWidget>
 */
final class WidgetRepository extends AbstractEloquentRepository
{
    public function __construct(RepoWidget $model)
    {
        parent::__construct($model);
    }
}
