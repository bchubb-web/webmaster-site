<?php

declare(strict_types=1);

use App\Application\Controllers\Homepage;
use App\Application\Controllers\TestController;
use App\Application\Controllers\SitemapRequestHandler;
use Webmaster\Http\Routing\RouteBuilder;

return function (RouteBuilder $router): RouteBuilder {

    $router->add(
        uri: '/',
        target: Homepage::class,
        methods: ['GET'],
        name: 'homepage'
    );

    $router->add(
        uri: '/test',
        target: [TestController::class, 'index'],
        methods: ['GET'],
        name: 'test'
    );

    $router->add(
        uri: '/clearcache',
        target: [TestController::class, 'index'],
        methods: ['GET'],
        name: 'manage.index'
    );

    $router->add(
        uri: '/sitemap.xml',
        target: SitemapRequestHandler::class,
        methods: ['GET'],
        name: 'sitemap'
    );

    return $router;
};

