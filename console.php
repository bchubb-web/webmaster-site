<?php

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/vendor/autoload.php';

$configInit = require_once ROOT . '/config/webmaster/config.php';
$config = $configInit();

$userConfig = require_once ROOT . '/config/config.php';
$config = $userConfig($config);

// core container
$containerInit = require_once ROOT . '/config/webmaster/container.php';
$container = $containerInit($config);

// site dependencies
$definitions = require_once ROOT . '/config/container.php';
$container = $definitions($container);

$app = $container->get(\Webmaster\Entrypoint\Console::class);

exit($app->handle());
