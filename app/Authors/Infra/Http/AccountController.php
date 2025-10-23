<?php

declare(strict_types=1);

namespace App\Authors\Infra\Http;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Webmaster\View\SimpleRenderer;
use Psr\Http\Message\ResponseFactoryInterface;

class AccountController
{
    public function __construct(
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly EntityManager $entityManager,
        private readonly CompiledUrlGenerator $urlGenerator,
    )
    {
    }

    public function profile(ServerRequestInterface $request): ResponseInterface
    {
        $threads = $this->entityManager->getRepository(Account::class)->findBy([], ['created' => 'DESC']);

        $view = $this->view->stream(
            'threads/index.twig',
            [
                'url' => $this->urlGenerator,
            ]
        );
        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($view)
        ;
    }

    protected function notFound(): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(404)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('threads/not-found.twig'))
        ;
    }
}
