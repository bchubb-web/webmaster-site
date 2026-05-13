<?php

declare(strict_types=1);

use App\Articles\Domain\Entity\Article;
use App\Articles\ViewArticle;
use App\Authors\Infra\Http\SignUpHandler as AuthorSignUp;
use App\Articles\Infra\Http\ArticleController;
use App\Application\Controllers\SitemapRequestHandler;
use App\Application\Controllers\TestController;
use App\Homepage\RequestHandler;
use Doctrine\ORM\EntityManager;
use Webmaster\Http\Routing\RouteBuilder;

return function (RouteBuilder $router): RouteBuilder {
    $router->add(
        uri: '/',
        target: RequestHandler::class,
        methods: ['GET'],
        name: 'homepage'
    );

    $router->add(
        uri: '/authors/sign-up',
        target: AuthorSignUp::class,
        methods: ['GET', 'POST'],
        name: 'authors.sign-up',
    );

    $router->add(
        uri: '/article/{id<\d+>}',
        target: ViewArticle::class,
        methods: ['GET'],
        name: 'articles.view'
    )->bind('id', function (int $value, EntityManager $em) {
        return $em->getRepository(Article::class)->find($value);
    });

    $router->add(
        uri: '/publish',
        target: [ArticleController::class, 'new'],
        methods: ['GET'],
        name: 'articles.new'
    );

    $router->add(
        uri: '/publish',
        target: [ArticleController::class, 'publish'],
        methods: ['POST'],
        name: 'articles.publish'
    );

    // LEGACY

    $router->add(
        uri: '/sitemap.xml',
        target: SitemapRequestHandler::class,
        methods: ['GET'],
        name: 'sitemap'
    );

    $router->add(
        uri: '/clearcache',
        target: [TestController::class, 'index'],
        methods: ['GET'],
        name: 'manage.index'
    );

    return $router;
};
