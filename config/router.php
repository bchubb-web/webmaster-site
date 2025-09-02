<?php

declare(strict_types=1);

use Webmaster\Http\Routing\RouteBuilder;

return function (RouteBuilder $router): RouteBuilder {
    $router->add(
        uri: '/',
        target: \App\Application\Controllers\Homepage::class,
        methods: ['GET'],
        name: 'homepage'
    );

    return $router;
};

