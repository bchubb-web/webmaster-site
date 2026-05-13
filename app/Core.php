<?php

declare(strict_types=1);

namespace App;

class Core extends \Webmaster\Core
{
    #[\Override]
    public function getContainerDefinitionFiles(): array
    {
        return [
            ROOT . '/config/container/doctrine.php',
        ];
    }

    public function getProviders(): array
    {
        return [
            //ROOT . '/config/container/ux-components.php',
        ];
    }
}
