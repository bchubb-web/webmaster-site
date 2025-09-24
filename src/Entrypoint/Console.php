<?php

declare(strict_types=1);

namespace Webmaster\Entrypoint;

use Psr\Container\ContainerInterface;

class Console implements EntrypointInterface
{
    protected array $arguments = [];
    public function __construct(
        private readonly \Webmaster\Core $core,
        private readonly ContainerInterface $container,
    ) {
        $this->arguments = $_SERVER['argv'] ?? [];
    }

    public function handle(): int
    {
        return 0;
    }

    public function getCore(): \Webmaster\Core
    {
        return $this->core;
    }
}
