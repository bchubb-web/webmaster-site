<?php

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Webmaster\View\SimpleRenderer;

class Homepage
{
    public function __construct(
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {
    }
    public function __invoke(): ResponseInterface
    {
        return $this
            ->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('index.twig'))
        ;
    }
}
