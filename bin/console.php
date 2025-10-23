#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';

return [
    \Webmaster\Entrypoint\Console::class,
    \App\Core::class,
];
