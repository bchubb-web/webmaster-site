<?php

use DebugBar\DataCollector\TimeDataCollector;

define('REQUEST_START', microtime(true));

$autoloadStart = microtime(true);

require_once __DIR__ . '/../vendor/autoload.php';

$configStart = microtime(true);

require_once __DIR__ . '/../config/constants.php';

$configInit = require_once ROOT . '/config/webmaster/config.php';
$config = $configInit();

$userConfig = require_once ROOT . '/config/config.php';
$config = $userConfig($config);

$containerStart = microtime(true);

// core container
$containerInit = require_once ROOT . '/config/webmaster/container.php';
$container = $containerInit($config);

// site dependencies
$containerBuilder = require_once ROOT . '/config/container.php';
$container = $containerBuilder($container);

$containerEnd = microtime(true);


/** @var TimeDataCollector */
$timeline = $container->get(TimeDataCollector::class);

$timeline->addMeasure(
    'PHP Initialization',
    $_SERVER["REQUEST_TIME_FLOAT"],
    REQUEST_START,
);

$timeline->addMeasure(
    'Autoload',
    $autoloadStart,
    $configStart,
);

$timeline->addMeasure(
    'Build Config',
    $configStart,
    $containerStart,
);

$timeline->addMeasure(
    'Define Container',
    $containerStart,
    $containerEnd,
);

$app = $container->get(\Webmaster\Entrypoint\Web::class);

exit($app->handle());
