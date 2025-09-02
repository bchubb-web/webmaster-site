<?php

declare(strict_types=1);

use League\Container\Container;

return function (Container $container) {
    $queue = $container->get('League\Tactician\CommandBus');
    $queue->before('Webmaster\Middleware\SessionMiddleware');
    $queue->before('Webmaster\Middleware\CsrfMiddleware');
    $queue->before('Webmaster\Middleware\RouterMiddleware', [
        'router' => $container->get('Symfony\Component\Routing\Router'),
    ]);
    $queue->before('Webmaster\Middleware\ControllerMiddleware', [
    return $queue;
};
