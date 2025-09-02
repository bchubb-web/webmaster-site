<?php

use Webmaster\Entrypoint\Console;

require_once __DIR__ . '/vendor/autoload.php';

$containerBuilder = require_once __DIR__ . '/config/container.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = $containerBuilder();

$app = $container->get(Console::class);

$app->handle();

echo 'Hello, World!';
