<?php

declare(strict_types=1);

namespace Webmaster;

interface CoreInterface
{
    public function __construct();
    /* config files */

    public function getContainerDefinitionFiles(): array;

    public function getWebmasterContainerDefinition(): string;

    /* config files */

    public function getConfigDir(): string;

    public function getWebmasterConfigDir(): string;

    /* cache dir */

    public function getCacheDir(): string;
}
