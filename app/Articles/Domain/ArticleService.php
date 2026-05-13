<?php

declare(strict_types=1);

namespace App\Articles\Domain;

use App\Articles\Domain\Entity\Article;
use Doctrine\ORM\EntityManager;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;

class ArticleService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected const ENTITY_CLASS = Article::class;

    public function __construct(
    ) {
    }

    public function createFromPublishRequest(ServerRequestInterface $request, EntityManager $em): Article
    {
        $instance = $this->getNewInstance();

        $publishFields = [
            'title',
            'content',
            //'author_id',
        ];

        $postArticle = $request->getParsedBody()['article'] ?? [];

        foreach ($publishFields as $field) {
            if (!array_key_exists($field, $postArticle)) {
                throw new \Exception('field ' . $field . ' not found for publish');
            }

            $instance->$field = $postArticle[$field];
        }

        $em->persist($instance);

        return $instance;
    }

    protected function getNewInstance(): Article
    {
        return $this->getContainer()->getNew(self::ENTITY_CLASS);
    }
}
