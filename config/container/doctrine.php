<?php

declare(strict_types=1);

use App\Application\EntityRegistry;
use App\Articles\Domain\Entity\Article;
use App\Domain\Entity;
use Doctrine\ORM\Configuration as OrmConfig;
use Doctrine\ORM\EntityManager;
use League\Config\Configuration as SiteConfig;
use League\Container\Container;
use App\Shared\Domain\Entity as SharedEntity;

return function (Container $container): Container {

    $container->addShared(EntityRegistry::class);

    $container
        ->extend(EntityRegistry::class)
        //->addMethodCall('register', [Entity\Thread::class])
        ->addMethodCall('register', [SharedEntity\Author::class])
        ->addMethodCall('register', [Article::class])
    ;

    $container
        ->addShared(OrmConfig::class, function (EntityRegistry $registry) {
            $ormConfig = $registry->setup();
            $ormConfig->enableNativeLazyObjects(true);
            return $ormConfig;
        })
        ->addArguments([
            EntityRegistry::class,
        ])
    ;

    $container
        ->addShared(
            \Doctrine\DBAL\Connection::class, function (SiteConfig $config, OrmConfig $ormConfig) {
                $connection = \Doctrine\DBAL\DriverManager::getConnection(
                    $config->get('db.connection', []),
                    $ormConfig,
                );

                return $connection;
            })
        ->addArguments([
            SiteConfig::class,
            OrmConfig::class,
        ])
    ;

    $container
        ->add(\Doctrine\DBAL\Schema\AbstractSchemaManager::class, function (\Doctrine\DBAL\Connection $connection) {
            return $connection->createSchemaManager();
        })
        ->addArguments([
            \Doctrine\DBAL\Connection::class,
        ])
    ;

    $container
        ->addShared(EntityManager::class)
        ->addArguments([
            \Doctrine\DBAL\Connection::class,
            OrmConfig::class,
        ])
    ;

    return $container;
};
