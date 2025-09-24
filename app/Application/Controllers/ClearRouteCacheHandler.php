<?php

namespace App\Application\Controllers;

use Webmaster\Http\Routing\Cache\RedisCache;

class ClearRouteCacheHandler
{
    public function __construct(
        private readonly RedisCache $cache,
    ) {
    }

    public function __invoke(): string
    {
        $this->cache->clear();

        return 'cache cleared';
    }
}
