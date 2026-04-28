<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Tests;

use Illuminate\Support\Facades\Cache;
use SlashDw\CoreKit\Cache\TaggedCacheInvalidator;

final class TaggedCacheInvalidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_flush_by_tags_empty_does_not_flush_tagged_entries(): void
    {
        Cache::tags(['keep'])->put('k', 'v', 600);

        (new TaggedCacheInvalidator(Cache::store()))->flushByTags([]);

        $this->assertSame('v', Cache::tags(['keep'])->get('k'));
    }

    public function test_flush_by_tags_flushes_matching_tag_group(): void
    {
        Cache::tags(['users'])->put('uid', 'secret', 600);
        Cache::put('global', 'ok', 600);

        (new TaggedCacheInvalidator(Cache::store()))->flushByTags(['users']);

        $this->assertNull(Cache::tags(['users'])->get('uid'));
        $this->assertSame('ok', Cache::get('global'));
    }

    public function test_forget_many_forgets_each_key(): void
    {
        Cache::put('k1', 'a', 600);
        Cache::put('k2', 'b', 600);

        (new TaggedCacheInvalidator(Cache::store()))->forgetMany(['k1', 'k2']);

        $this->assertNull(Cache::get('k1'));
        $this->assertNull(Cache::get('k2'));
    }
}
