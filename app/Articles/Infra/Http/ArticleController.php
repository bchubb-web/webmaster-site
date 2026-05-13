<?php

namespace App\Articles\Infra\Http;

use App\Articles\Domain\ArticleService;
use App\Articles\Domain\Entity\Article;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Webmaster\View\SimpleRenderer;

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
        $repo = $this->entityManager->getRepository(Article::class);
        $articles = $repo->findAll();
        return $this
            ->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('articles/index.twig', [
                'articles' => $articles,
                'url' => $this->urlGenerator,
            ]))
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

        $this->entityManager->flush();

        $location = $this->urlGenerator->generate('articles.view', [
            'id' => $article->id,
        ]);
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
