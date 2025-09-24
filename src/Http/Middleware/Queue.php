<?php

declare(strict_types=1);

namespace Webmaster\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware queue to manage a list of middleware components.
 */
class Queue
{
    /** @param Array<MiddlewareInterface|RequestHandlerInterface|string> $queue */
    public function __construct(private array $queue = [])
    {
    }

    public function setQueue(array $queue): void
    {
        $this->queue = $queue;
    }

    public function next(): MiddlewareInterface|RequestHandlerInterface|string
    {
        $entry = array_shift($this->queue) ?: null;
    }

    protected function resolve(mixed $entry): MiddlewareInterface|RequestHandlerInterface
    {
        if (is_string($entry)) {
            return new $entry();
        }

        return $entry;
    }
}
