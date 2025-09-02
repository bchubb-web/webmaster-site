<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Container\Argument\Literal\ArrayArgument as ArrayArg;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\ContainerRuntimeLoader;


use Twig\Environment;
use Twig\Loader\LoaderInterface;

return function (): Container {
    $container = new Container(defaultToOverwrite: true);

    $container->delegate(new ReflectionContainer(cacheResolutions: true));

    $container->addShared(Container::class, $container);
    $container
        ->addShared(ContainerInterface::class, fn ($c) => $c)
        ->addArgument(Container::class)
    ;

    // Register core services here
    configuration($container);
    twig($container);
    http($container);

    return $container;
};

function twig(Container $container): Container
{
    $container
        ->addShared(
            Environment::class,
        )
        ->addArgument(LoaderInterface::class)
        /*->addArgument([
            'debug' => true,
            'strict_variables' => true,
        ])*/
        ->addMethodCall('addRuntimeLoader', [
            ContainerRuntimeLoader::class,
        ])
    ;

    $container
        ->addShared(LoaderInterface::class, function (array $config) {
            return new FilesystemLoader($config['load_from']);
        })
        ->addArgument(new ArrayArg([
            'load_from' => [
                __DIR__ . '/../../views',
            ],
        ]))
    ;

    return $container;
}

/**
 * Registers core psr7/http services.
 */
function http(Container $container): Container
{
    $container
        ->add(
            ServerRequestInterface::class,
            fn ($creator) => $creator->fromGlobals(),
        )
        ->addArgument(ServerRequestCreator::class)
    ;

    $container
        ->addShared(
            Nyholm\Psr7\Factory\Psr17Factory::class,
        )
    ;

    $container
        ->addShared(
            Psr\Http\Message\StreamFactoryInterface::class,
            fn ($factory) => $factory,
        )
        ->addArgument(Nyholm\Psr7\Factory\Psr17Factory::class)
    ;

    $container
        ->addShared(
            Psr\Http\Message\UploadedFileFactoryInterface::class,
            fn ($factory) => $factory,
        )
        ->addArgument(Nyholm\Psr7\Factory\Psr17Factory::class)
    ;

    $container
        ->addShared(
            Psr\Http\Message\UriFactoryInterface::class,
            fn ($factory) => $factory,
        )
        ->addArgument(Nyholm\Psr7\Factory\Psr17Factory::class)
    ;

    $container
        ->addShared(
            Psr\Http\Message\RequestFactoryInterface::class,
            fn ($factory) => $factory,
        )
        ->addArgument(Nyholm\Psr7\Factory\Psr17Factory::class)
    ;

    $container
        ->addShared(
            Psr\Http\Message\ResponseFactoryInterface::class,
            fn ($factory) => $factory,
        )
        ->addArgument(Nyholm\Psr7\Factory\Psr17Factory::class)
    ;

    $container
        ->addShared(
            ServerRequestFactoryInterface::class,
            fn ($factory) => $factory,
        )
        ->addArgument(Nyholm\Psr7\Factory\Psr17Factory::class)
    ;

    return $container;
}

function configuration(Container $container): Container
{
    $container
        ->addShared('config', function () {
            return require __DIR__ . '/config.php';
        })
    ;

    return $container;
}
