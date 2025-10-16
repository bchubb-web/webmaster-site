<?php

declare(strict_types=1);

use App\Application\EntityRegistry;
use Doctrine\ORM\Configuration as OrmConfig;
use Doctrine\ORM\EntityManager;
use League\Config\Configuration as SiteConfig;
use League\Container\Container;
use App\Domain\Entity;

return function (Container $container): Container {
    $container
        ->extend(EntityRegistry::class)
        ->addMethodCall('register', [Entity\Thread::class])
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
