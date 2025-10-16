<?php

declare(strict_types=1);

namespace Webmaster\Entrypoint;

abstract class AbstractEntrypoint implements EntrypointInterface
{
    protected \Webmaster\Core $core;

    protected \Psr\Container\ContainerInterface $container;

    public function setCore(\Webmaster\Core $core): void
    {
        $this->core = $core;

        $this->container = $this->core->getContainer();
    }

    public function getCore(): \Webmaster\Core
    {
        return $this->core;
    }
}
