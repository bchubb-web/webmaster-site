<?php

declare(strict_types=1);

namespace Webmaster\Http\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RedirectHandler implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly string $basePath = '/',
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        // Ensure the base path ends with a single slash
        $normalizedBasePath = rtrim($this->basePath, '/') . '/';

        if (strpos($path, $normalizedBasePath) !== 0) {
            // Redirect to the correct base path
            $newUri = $uri->withPath($normalizedBasePath . ltrim($path, '/'));
            return $this->responseFactory
                ->createResponse(301)
                ->withHeader('Location', (string)$newUri);
        }

        return $handler->handle($request);
    }
}
