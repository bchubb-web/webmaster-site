<?php

declare(strict_types=1);

namespace Webmaster\Entrypoint;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

class Console extends AbstractEntrypoint
{
    protected array $arguments = [];
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
        $this->arguments = $_SERVER['argv'] ?? [];
    }

    public function handle(): int
    {
        ConsoleRunner::run(
            new SingleManagerProvider($this->entityManager),
            []
        );

        return 0;
    }
}
