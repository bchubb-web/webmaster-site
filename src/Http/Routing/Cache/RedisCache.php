<?php

declare(strict_types=1);

namespace Webmaster\Http\Routing\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper;
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
        $item = $this->cache->getItem('webmaster.routing.matcher.cache');

        if (!$item->isHit()) {
            return null;
        }

        $routes = $item->get();

        return $routes;
    }

    public function getGeneratorRoutes(): ?array
    {
        $item = $this->cache->getItem('webmaster.routing.generator.cache');

        if (!$item->isHit()) {
            return null;
        }

        $routes = $item->get();

        return $routes;
    }

    public function set(RouteCollection $routes): bool
    {
        $compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
        $dumpedRoutes = (new CompiledUrlGeneratorDumper($routes))->getCompiledRoutes();

        $matchItem = $this->cache->getItem('webmaster.routing.matcher.cache');
        $dumpItem = $this->cache->getItem('webmaster.routing.generator.cache');

        /*$matchItem->tag('webmaster.routing.cache');
        $dumpItem->tag('webmaster.routing.cache');*/

        $matchItem->set($compiledRoutes);
        $dumpItem->set($dumpedRoutes);
        $matchItem->expiresAfter(3600);
        $dumpItem->expiresAfter(3600);

        $this->cache->save($dumpItem);
        return $this->cache->save($matchItem);
    }

    public function clear(): bool
    {
        return $this->cache->clear('webmaster.routing');
    }

}

