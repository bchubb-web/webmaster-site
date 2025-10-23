<?php

declare(strict_types=1);

namespace App;

use \Webmaster\Core as WebmasterCore;

class Core extends WebmasterCore
{
    #[\Override]
    public function getContainerDefinitionFiles(): array
    {
        return [
            ROOT . '/config/container/doctrine.php',
        ];
    }
}
