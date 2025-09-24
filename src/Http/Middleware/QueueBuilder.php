<?php

declare(strict_types=1);

namespace Webmaster\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware queue builder to create and order middleware handlers
 */
class QueueBuilder
{
    /** @var Array<MiddlewareInterface|RequestHandlerInterface|string> */
    private array $entries = [];

    public function getQueue(): Queue
    {
        return new Queue($this->entries);
    }
}
