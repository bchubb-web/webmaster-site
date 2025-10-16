<?php

namespace App\Application;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMSetup;

class EntityRegistry
{
    private array $entities = [];

    public function register(object $entityClass): void
    {
        $reflection = new \ReflectionClass($entityClass);
        $this->entities[dirname($reflection->getFileName())] = get_class($entityClass);
    }

    public function setup(): Configuration
    {
        $ormConfig = ORMSetup::createAttributeMetadataConfig(
            array_keys($this->entities),
            true,
        );

        return $ormConfig;
    }
}
