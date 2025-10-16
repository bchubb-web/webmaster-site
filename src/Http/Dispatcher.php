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
use function array_intersect_key;

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
        $target = $request->getAttribute('_target');

        $instance = is_array($target)
            ? $this->container->get($target[0])
            : $this->container->get($target);

        $this->timeline->startMeasure('dispatch', 'Dispatching');
        if (is_array($target)) {

            $reflection = new \ReflectionMethod($instance, $target[1]);
            $parameters = $reflection->getParameters();
            // foreach parameter pull from container
            $args = [];
            foreach ($parameters as $parameter) {
                $type = $parameter->getType();

                if ($type->__toString() === ServerRequestInterface::class) {
                    $args[] = $request;
                } elseif ($type && !$type->isBuiltin() && $this->container->has($type->getName())) {
                    $args[] = $this->container->get($type->getName());
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();
                } else {
                    throw new \RuntimeException('Cannot resolve parameter ' . $parameter->getName());
                }
            }
            $response = $instance->{$target[1]}(...$args);
        } elseif ($instance instanceof RequestHandlerInterface) {
            $response = $instance->handle($request);
        } else { // Assume it's a callable, di wont work atm
            $response = $instance(...array_values($this->matched));
        }
        $this->timeline->stopMeasure('dispatch');

        if (is_string($response)) {
            $response = $this
                ->responseFactory
                ->createResponse(200)
                ->withHeader('Content-Type', 'text/html')
                ->withBody(Stream::create($response))
            ;
        }

        return $response;
    }
}
