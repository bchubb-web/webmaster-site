<?php

declare(strict_types=1);

namespace App\Authors\Application;

use App\Shared\Domain\Entity\Author;
use Doctrine\ORM\EntityManager;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use function array_key_exists;

class AuthorService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(
    ) {
    }

    public function createFromSignUpRequest(ServerRequestInterface $request, EntityManager $em): Author
    {
        $instance = $this->getNewInstance();

        $signUpFields = [
            'email',
            'username',
            'password',
        ];

        $postAuthor = $request->getParsedBody()['author'] ?? [];

        foreach ($signUpFields as $field) {
            if (!array_key_exists($field, $postAuthor)) {
                throw new \Exception('field ' . $field . ' not found for signup');
            }

            $instance->$field = $postAuthor[$field];
        }

        $em->persist($instance);

        return $instance;
    }

    protected function getNewInstance(): Author
    {
        return $this->getContainer()->getNew(Author::class);
    }
}

