<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use SlashDw\CoreKit\Tests\Fixtures\RepoWidget;
use SlashDw\CoreKit\Tests\Fixtures\WidgetBadFilter;
use SlashDw\CoreKit\Tests\Fixtures\WidgetNameFilter;
use SlashDw\CoreKit\Tests\Fixtures\WidgetRepository;

final class AbstractEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function test_apply_filter_returns_builder(): void
    {
        RepoWidget::create([
            'id' => 'w1',
            'name' => 'alpha',
        ]);
        RepoWidget::create([
            'id' => 'w2',
            'name' => 'beta',
        ]);

        $repo = new WidgetRepository(new RepoWidget);
        $query = $repo->applyFilter(new WidgetNameFilter('beta'));

        $this->assertCount(1, $query->get());
        $this->assertSame('beta', $query->first()?->name);
    }

    public function test_apply_filter_throws_when_filter_returns_non_builder(): void
    {
        $repo = new WidgetRepository(new RepoWidget);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Filter::apply() must return Builder');

        $repo->applyFilter(new WidgetBadFilter);
    }

    public function test_find_by_id_and_find_or_fail(): void
    {
        RepoWidget::create(['id' => 'a1', 'name' => 'one']);

        $repo = new WidgetRepository(new RepoWidget);

        $this->assertSame('one', $repo->findById('a1')?->name);
        $this->assertNull($repo->findById('missing'));
        $this->assertSame('one', $repo->findOrFail('a1')->name);

        $this->expectException(ModelNotFoundException::class);
        $repo->findOrFail('missing');
    }

    public function test_find_for_update_within_transaction(): void
    {
        RepoWidget::create(['id' => 'lock1', 'name' => 'locked']);

        $repo = new WidgetRepository(new RepoWidget);

        DB::transaction(function () use ($repo): void {
            $row = $repo->findForUpdate('lock1');
            $this->assertInstanceOf(RepoWidget::class, $row);
            $this->assertSame('locked', $row->name);
        });
    }

    public function test_create_update_delete_round_trip(): void
    {
        $repo = new WidgetRepository(new RepoWidget);

        $created = $repo->create(['id' => 'c1', 'name' => 'new']);
        $this->assertSame('new', $created->fresh()?->name);

        $this->assertTrue($repo->updateById('c1', ['name' => 'renamed']));
        $this->assertSame('renamed', $repo->findById('c1')?->name);

        $model = $repo->findById('c1');
        $this->assertInstanceOf(RepoWidget::class, $model);
        $this->assertTrue($repo->updateByModel($model, ['name' => 'via-model']));
        $updated = $repo->findById('c1');
        $this->assertInstanceOf(RepoWidget::class, $updated);
        $this->assertSame('via-model', $updated->name);

        $this->assertTrue($repo->deleteById('c1'));
        $this->assertNull($repo->findById('c1'));

        $second = $repo->create(['id' => 'c2', 'name' => 'del']);
        $this->assertTrue($repo->deleteByModel($second));
        $this->assertNull($repo->findById('c2'));
    }

    public function test_insert_many_empty_and_rows(): void
    {
        $repo = new WidgetRepository(new RepoWidget);
        $now = now()->toDateTimeString();

        $this->assertTrue($repo->insertMany([]));

        $this->assertTrue($repo->insertMany([
            [
                'id' => 'i1',
                'name' => 'bulk',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]));

        $this->assertSame('bulk', $repo->findById('i1')?->name);
    }
}
