<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use Webmaster\Http\Routing\Cache\RedisCache;

class TestController
{
    public function index(): string
    {
        $this->cache->clear();
        return 'Hello, World!';
    }
    public function __construct(
        private readonly RedisCache $cache,
    ) {
    }
}
