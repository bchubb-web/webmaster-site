<?php

declare(strict_types=1);

namespace App\Authors\Infra\Http;

use App\Authors\Application\AuthorService;
use App\Shared\Domain\Entity\Author;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Webmaster\View\SimpleRenderer;

class SignUpHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly SimpleRenderer $view,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly EntityManager $em,
        private readonly CompiledUrlGenerator $urlGenerator,
        private readonly AuthorService $authorService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ('POST' === $request->getMethod()) {
            $authorPost = $request->getParsedBody()['author'];
            $authorRepo = $this->em->getRepository(Author::class);

            if ($authorRepo->findOneBy(['email' => $authorPost['email']])) {
                throw new \Exception('author exists');
            }

            $author = $this->authorService->createFromSignUpRequest($request, $this->em);

            $this->em->flush();

            return $this
                ->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $this->urlGenerator->generate('authors.sign-in'))
            ;
        }

        return $this
            ->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($this->view->stream('authors/login.twig'))
        ;
    }
}
