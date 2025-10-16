<?php

declare(strict_types=1);

namespace App\Infra\Http\Controller;

use App\Domain\Entity\Thread;
use Doctrine\ORM\EntityManager;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Webmaster\View\SimpleRenderer;
use Psr\Http\Message\ResponseFactoryInterface;

class ThreadController
{
    public function __construct(
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly EntityManager $entityManager,
        private readonly CompiledUrlGenerator $urlGenerator,
    )
    {
    }

    public function index(): ResponseInterface
    {
        $threads = $this->entityManager->getRepository(Thread::class)->findBy([], ['created' => 'DESC']);

        $view = $this->view->stream(
            'threads/index.twig',
            [
                'threads' => $threads,
                'url' => $this->urlGenerator,
            ]
        );
        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($view)
        ;
    }

    public function show(
        ServerRequestInterface $request,
    ): ResponseInterface
    {
        $slug = $request->getAttribute('slug');
        $thread = $this->entityManager->getRepository(Thread::class)->findOneBy(['slug' => $slug]);

        if (null === $thread) {
            return $this->notFound();
        }

        $view = $this->view->stream(
            'threads/show.twig',
            compact('thread')
        );
        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($view)
        ;
    }

    public function new(
        ServerRequestInterface $request,
    ): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {

            $name = $request->getParsedBody()['name'] ?? null;
            $slug = $request->getParsedBody()['slug'] ?? null;
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));

            $thread = new Thread();
            $thread->title = $name;
            $thread->slug = $slug;
            $this->entityManager->persist($thread);
            $this->entityManager->flush();

            $uri = $this->urlGenerator->generate('thread.show', ['slug' => $slug]);
            return $this
                ->responseFactory
                ->createResponse(302)
                ->withHeader('Content-Type', 'text/plain')
                ->withHeader('Location', $uri)
            ;
        }

        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('threads/new.twig'));

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
