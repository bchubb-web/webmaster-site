<?php

declare(strict_types=1);

use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Webmaster\Http\Routing\Cache\RedisCache;
use \DebugBar\DataCollector as Collector;
use League\Config\Configuration;
use League\Container\Container;
use League\Container\Argument\Literal\IntegerArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\ReflectionContainer;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\ContainerRuntimeLoader;


use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Webmaster\Debug\DebugBar;

return function (Configuration $config): Container {
    $container = new Container(defaultToOverwrite: true);

    $container->delegate(new ReflectionContainer(cacheResolutions: true));

    $container->addShared(Container::class, $container);
    $container
        ->addShared(ContainerInterface::class, fn ($c) => $c)
        ->addArgument(Container::class)
    ;

    $container->addShared(Configuration::class, $config);

    // Register core services here
    twig($container);
    http($container);
    cache($container);
    debug($container);
    events($container);

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
        ->addShared(LoaderInterface::class, function (Configuration $config) {
            return new FilesystemLoader($config->get('view.load_from', []));
        })
        ->addArgument(Configuration::class)
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

    $container
        ->addShared(
            Relay\RelayBuilder::class,
        )
    ;

    $container
        ->add(
            CompiledUrlGenerator::class,
            function (RedisCache $cache) {
                return new CompiledUrlGenerator(
                    $cache->getGeneratorRoutes() ?? [],
                    new Symfony\Component\Routing\RequestContext()
                );
            }
        )
        ->addArgument(RedisCache::class)
    ;

    return $container;
}

function cache(Container $container): Container
{
    $container
        ->addShared(
            \Predis\ClientInterface::class,
            fn () => RedisAdapter::createConnection(
                'redis://redis:6379',
                [ 'class' => \Predis\Client::class, ]
            )
        )
    ;

    $container
        ->addShared(RedisAdapter::class)
        ->addArgument(\Predis\ClientInterface::class)
        ->addArgument(new StringArgument('webmaster'))
        ->addArgument(new IntegerArgument(3600))
    ;

    return $container;
}

function debug(Container $container): Container
{
    $container
        ->addShared(
            DebugBar::class,
        )
    ;

    foreach ([
        Collector\MessagesCollector::class,
        Collector\TimeDataCollector::class,
        Collector\RequestDataCollector::class,
        Collector\MemoryCollector::class,
        Collector\PhpInfoCollector::class,
        Collector\ExceptionsCollector::class,
    ] as $collector) {
        $container->addShared($collector);

        $container
            ->extend(DebugBar::class)
            ->addMethodCall('addCollector', [$collector])
        ;
    }


    $container
        ->addShared(
            \Middlewares\Debugbar::class,
        )
        ->addArgument(DebugBar::class)
        ->addArgument(Psr\Http\Message\ResponseFactoryInterface::class)
        ->addArgument(Psr\Http\Message\StreamFactoryInterface::class)
        ->addMethodCall('inline')
    ;
    return $container;
}
function events(Container $container): Container
{
    $container
        ->addShared(
            Symfony\Component\EventDispatcher\EventDispatcher::class,
        )
    ;

    $container
        ->addShared(
            Psr\EventDispatcher\EventDispatcherInterface::class,
            fn ($dispatcher) => $dispatcher,
        )
        ->addArgument(Symfony\Component\EventDispatcher\EventDispatcher::class)
    ;

    return $container;
}
