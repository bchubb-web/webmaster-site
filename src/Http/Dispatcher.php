<?php

declare(strict_types=1);

namespace Webmaster\Http;

use DebugBar\DataCollector\TimeDataCollector;
use Nyholm\Psr7\Stream;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Dispatcher implements RequestHandlerInterface
{
    private array $matched = [];

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly TimeDataCollector $timeline,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $target = $this->matched['_target'];

        $instance = is_array($target)
            ? $this->container->get($target[0])
            : $this->container->get($target);

        $this->timeline->startMeasure('dispatch', 'Dispatching');
        if (is_array($target)) {
            $response = $instance->{$target[1]}(...array_values($this->matched));
        } elseif ($instance instanceof RequestHandlerInterface) {
            $response = $instance->handle($request);
        } else {
            $response = $instance(...array_values($this->matched));
        }
        $this->timeline->stopMeasure('dispatch');

        if (is_string($response)) {
            $response = $this
                ->responseFactory
                ->createResponse(200)
                ->withBody(Stream::create($response))
            ;
        }

        return $response;
    }

    public function setMatched(array $matched): void
    {
        $this->matched = $matched;
    }
}
