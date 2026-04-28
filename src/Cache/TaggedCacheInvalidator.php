<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Cache;

use Illuminate\Contracts\Cache\Repository;

final class TaggedCacheInvalidator implements CacheInvalidator
{
    public function __construct(
        private readonly Repository $cache,
    ) {}

    public function flushByTags(array $tags): void
    {
        if ($tags === []) {
            return;
        }

        $this->cache->tags($tags)->flush();
    }

    public function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            $this->cache->forget($key);
        }
    }
}
