<?php

declare(strict_types=1);

namespace Webmaster\Http\Routing;

use DebugBar\DataCollector\TimeDataCollector;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Router
{
    protected UrlMatcher $matcher;

    protected $startTime;

    public function __construct(
        protected readonly TimeDataCollector $timeline,
        protected RouteBuilder $routeBuilder,
    ) {
        $this->startTime = microtime(true);

        $this->matcher = new CompiledUrlMatcher(
            $this->routeBuilder->getRoutes(),
            new RequestContext(),
        );
    }

    public function match(ServerRequestInterface $request): ServerRequestInterface
    {
        $this->timeline->startMeasure('routing', 'Routing');

        $matched = $this->matcher->match(
            $request->getUri()->getPath()
        );

        $this->timeline->stopMeasure('routing');

        $target = $matched['_target'];
        $parameters = $matched;

        unset($parameters['_target']);
        unset($parameters['_route']);

        $request = $request->withAttribute('_target', $target);
        foreach ($parameters as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request->withAttribute('matched', $matched);
    }
}
