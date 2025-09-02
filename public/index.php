<?php

require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . '/../vendor/autoload.php';

$configInit = require_once __DIR__ . '/../config/webmaster/config.php';
$config = $configInit();

$userConfig = require_once __DIR__ . '/../config/config.php';
$config = $userConfig($config);

// core container
$containerInit = require_once __DIR__ . '/../config/webmaster/container.php';
$container = $containerInit();

// site dependencies
$containerBuilder = require_once __DIR__ . '/../config/container.php';
$container = $containerBuilder($container);

$app = $container->get(\Webmaster\Entrypoint\Web::class);

$app->handle();
