<?php

namespace App\Articles\Infra\Http;

use App\Articles\Domain\ArticleService;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Webmaster\View\SimpleRenderer;
use Psr\Http\Message\ResponseFactoryInterface;

class ArticleController
{
    public function __construct(
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly EntityManager $entityManager,
        private readonly CompiledUrlGenerator $urlGenerator,
        private readonly ArticleService $articleService,
    ) {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return $this
            ->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('articles/index.twig'))
        ;
    }

    public function new(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('articles/new.twig', [
                'publishUrl' => $this->urlGenerator->generate('articles.publish'),
            ]))
        ;
    }

    public function publish(ServerRequestInterface $request): ResponseInterface
    {
        assert($request->getMethod() === 'POST');

        $article = $this->articleService->createFromPublishRequest($request, $this->entityManager);

        $location = $this->urlGenerator->generate('homepage');
        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', $location)
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
