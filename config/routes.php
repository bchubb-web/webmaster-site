<?php

declare(strict_types=1);

use App\Application\Controllers\SitemapRequestHandler;
use App\Application\Controllers\TestController;
use App\Infra\Http\Controller\HomepageHandler;
use Webmaster\Http\Routing\RouteBuilder;

return function (RouteBuilder $router): RouteBuilder {
    $router->add(
        uri: '/',
        target: HomepageHandler::class,
        methods: ['GET'],
        name: 'homepage'
    );

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
