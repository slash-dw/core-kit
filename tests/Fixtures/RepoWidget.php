<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 */
final class RepoWidget extends Model
{
    protected $table = 'core_kit_repo_widgets';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];
}
