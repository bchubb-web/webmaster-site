<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';

return [
    \Webmaster\Entrypoint\Web::class,
    \App\Core::class,
];
