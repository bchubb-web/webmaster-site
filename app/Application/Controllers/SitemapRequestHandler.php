<?php

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;
use App\Domain\Sitemap\Generator as SitemapGenerator;

class SitemapRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly SitemapGenerator $sitemap,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200);

        $map = $this->sitemap->read()->build()->asXML();
        $response->getBody()->write($map);

        return $response->withHeader('Content-Type', 'application/xml');
    }
}
