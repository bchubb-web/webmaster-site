<?php

return function ($queueBuilder): void {
    $queueBuilder
        ->add(\App\Middleware\ExampleMiddleware::class);
    $queueBuilder->before(\App\Middleware\ExampleMiddleware::class, \App\Middleware\AnotherMiddleware::class);
    $queueBuilder->after(\App\Middleware\ExampleMiddleware::class, \App\Middleware\YetAnotherMiddleware::class);
    $queueBuilder->remove(\App\Middleware\ExampleMiddleware::class);
    $queueBuilder->prepend(\App\Middleware\PrependedMiddleware::class);
    $queueBuilder->append(\App\Middleware\AppendedMiddleware::class);
    $queueBuilder->clear();
};
