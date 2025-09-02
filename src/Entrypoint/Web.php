<?php

declare(strict_types=1);

namespace Webmaster\Entrypoint;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Webmaster\Http\Routing\RouteBuilder;

class Web implements EntrypointInterface
{
    private readonly UrlMatcher $matcher;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RouteBuilder $routeBuilder,
        private readonly ServerRequestInterface $request,
    ) {
        $this->matcher = new UrlMatcher(
            $this->routeBuilder->getRoutes(),
            new RequestContext(),
        );
    }

    public function handle(): void
    {
        $matched = $this->matcher->match(
            $this->request->getUri()->getPath()
        );

        $target = $matched['_target'];

        $instance = is_array($target)
            ? $this->container->get($target[0])
            : $this->container->get($target);

        if (is_array($target)) {
            $response = $instance->{$target[1]}(...array_values($matched));
        } else {
            $response = $instance(...array_values($matched));
        }

        // Send the response to the browser
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        echo $response->getBody();
    }
}
