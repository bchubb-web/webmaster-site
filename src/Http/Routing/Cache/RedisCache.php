<?php

declare(strict_types=1);

namespace Webmaster\Http\Routing\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\RouteCollection;

class RedisCache
{
    public function __construct(
        protected RedisAdapter $cache,
    ) {
    }

    public function get(): ?array
    {
        $item = $this->cache->getItem('webmaster.routing.cache');

        if (!$item->isHit()) {
            return null;
        }

        $routes = $item->get();

        return $routes;
    }

    public function set(RouteCollection $routes): bool
    {
        $compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();

        $item = $this->cache->getItem('webmaster.routing.cache');

        $item->set($compiledRoutes);
        $item->expiresAfter(3600);

        return $this->cache->save($item);
    }

    public function clear(): bool
    {
        return $this->cache->clear('webmaster.routing.cache');
    }

}

