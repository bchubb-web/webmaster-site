<?php

declare(strict_types=1);

namespace App\Articles;

use App\Articles\Domain\Entity\Article;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Webmaster\View\SimpleRenderer;

class ViewArticle
{
    public function __construct(
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly EntityManager $em,
        private readonly CompiledUrlGenerator $urlGenerator,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $repo = $this->em->getRepository(Article::class);
        $article = $repo->findOneBy(['id' => $request->getAttribute('id')]);

        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('articles/view.twig', [
                'article' => $article,
                'url' => $this->urlGenerator,
            ]))
        ;
    }
}
