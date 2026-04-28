<?php

declare(strict_types=1);

namespace SlashDw\CoreKit\Cache;

interface CacheInvalidator
{
    /**
     * @param  list<string>  $tags
     */
    public function flushByTags(array $tags): void;

    /**
     * @param  list<string>  $keys
     */
    public function forgetMany(array $keys): void;
}
