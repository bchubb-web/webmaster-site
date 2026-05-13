<?php

declare(strict_types=1);

namespace App\Infra\Http\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Webmaster\Http\Domain\Session;
use Webmaster\View\SimpleRenderer;

class HomepageHandler
{
    public function __construct(
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Session $session,
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        $this->session->foo = 'bar';

        return $this->responseFactory
            ->createResponse()
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('index.twig'))
        ;
    }
}
