<?php

namespace App\Homepage;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\UX\TwigComponent\Twig\ComponentLexer;
use Twig\Environment;
use Webmaster\View\Contract\HasViewRenderer;
use Webmaster\View\Trait\CanRenderViews;

class RequestHandler implements RequestHandlerInterface, HasViewRenderer, ContainerAwareInterface
{
    use CanRenderViews;
    use ContainerAwareTrait;

    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->getViewRenderer()->stream('index.twig'));
    }
}
