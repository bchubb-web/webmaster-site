<?php

declare(strict_types=1);

namespace Webmaster\Http\Routing;

use DebugBar\DataCollector\TimeDataCollector;
use Symfony\Component\Routing\RouteCollection;
use Webmaster\Http\Routing\Cache\RedisCache;

class RouteBuilder
{
    // compiled routes
    protected array $routes = [];

    public function __construct(
        protected readonly RedisCache $cache,
        protected readonly RouteCollection $rawRoutes,
        protected readonly TimeDataCollector $timeline,
    ) {
    }

    public function add(
        string $uri,
        string|array $target,
        array $methods = ['GET', 'POST'],
        ?string $name = null
    ): Definition {

        $uri = rtrim($uri, '/');

        $route = new Definition(
            $uri,
            ['_target' => $target],
            [],
            [],
            '',
            [],
            $methods,
        );

        $this->rawRoutes->add(
            $name ?? spl_object_id($route),
            $route
        );

        return $route;
    }

    public function build(): void
    {
        $routesFile = dirname(__DIR__, 3) . '/config/routes.php';

        if (file_exists($routesFile)) {
            $loader = require $routesFile;

            if (is_callable($loader)) {
                $loader($this);
            }
        }

        $this->cache->set($this->rawRoutes);
        $this->routes = $this->cache->get();

    }

    public function getRoutes(): array
    {
        $start = microtime(true);
        if (0 === count($this->routes)) {
            //if (null === $cached = $this->cache->get()) {
            if (true) {
                $this->build();
                $this->timeline->addMeasure('Build routes', $start, microtime(true));
            } else {
                $this->routes = $cached;
                $this->timeline->addMeasure('Retrieve cached routes', $start, microtime(true));

            }
        }
        return $this->routes;
    }

    public function getGeneratorRoutes(): ?array
    {
        return $this->cache->getGeneratorRoutes();
    }
}
