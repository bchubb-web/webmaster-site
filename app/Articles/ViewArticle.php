<?php

declare(strict_types=1);

namespace App\Articles;

use App\Articles\Domain\Entity\Article;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webmaster\View\SimpleRenderer;

class ViewArticle
{
    public function __construct(
        private readonly ServerRequestInterface $request,
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly EntityManager $em,
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        $repo = $this->em->getRepository(Article::class);
        $article = $repo->findOneBy(['id' => $this->request->getAttribute('id')]);
        dd($article);
        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('articles/view.twig'))
        ;
    }
}
