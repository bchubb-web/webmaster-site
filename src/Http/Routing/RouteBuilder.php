<?php

declare(strict_types=1);

namespace Webmaster\Http\Routing;

use Symfony\Component\Routing\RouteCollection;

class RouteBuilder
{
    public function __construct(
        protected RouteCollection $routes,
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

        $this->routes->add(
            $name ?? spl_object_id($route),
            $route
        );

        return $route;
    }

    public function build(): void
    {
        $routesFile = dirname(__DIR__, 3) . '/config/router.php';

        if (file_exists($routesFile)) {
            $loader = require $routesFile;

            if (is_callable($loader)) {
                $loader($this);
            }
        }
    }

    public function getRoutes(): RouteCollection
    {
        if (0 === $this->routes->count()) {
            $this->build();
        }
        return $this->routes;
    }
}
