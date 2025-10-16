<?php

declare(strict_types=1);

namespace Webmaster;

use League\Config\Configuration;

class Core implements CoreInterface
{
    private Configuration $configuration;

    private \Psr\Container\ContainerInterface $container;

    final public function __construct()
    {
    }

    public function getConfigDir(): string
    {
        return ROOT . '/config';
    }

    public function getWebmasterConfigDir(): string
    {
        return ROOT . '/config/webmaster';
    }

    protected function loadConfiguration(): Configuration
    {
        $configInit = require_once $this->getWebmasterConfigDir() . '/config.php';
        $config = $configInit();

        $applySiteConfig = require_once $this->getConfigDir() . '/config.php';
        $config = $applySiteConfig($config);

        return $config;
    }

    public function getConfiguration(): Configuration
    {
        if (!isset($this->configuration)) {
            $this->configuration = $this->loadConfiguration();
        }

        return $this->configuration;
    }


    public function getContainerDefinitionFiles(): array
    {
        return [
            ROOT . '/config/container.php',
        ];
    }

    public function getWebmasterContainerDefinition(): string
    {
        return ROOT . '/config/webmaster/container.php';
    }

    protected function createContainer(): \Psr\Container\ContainerInterface
    {
        $config = $this->getConfiguration();

        $init = include $this->getWebmasterContainerDefinition();

        $container = $init($config);

        foreach ($this->getContainerDefinitionFiles() as $file) {
            if (!is_file($file)) {
                throw new \RuntimeException("Container definition file not found: $file");
            }
            $definitions = include $file;
            $container = $definitions($container);
        }

        return $container;
    }

    public function getContainer(): \Psr\Container\ContainerInterface
    {
        if (!isset($this->container)) {
            $this->container = $this->createContainer();
        }

        return $this->container;
    }

    public function getCacheDir(): string
    {
        return ROOT . '/tmp/cache';
    }
}
