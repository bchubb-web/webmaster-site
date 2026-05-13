<?php

/**
 * Very much stolen from Symfony's autoload_runtime.php
 * I just think its neat.
 */

declare(strict_types=1);

define('ROOT', dirname(__DIR__, 1));

define('WEBMASTER', ROOT . '/vendor/bchubb-web/webmaster');

ini_set("error_log", dirname(__FILE__) . "/tmp/logs/errors.log");
ini_set("memory_limit", "256M");

if (true === require_once ROOT .'/vendor/autoload.php') {
    return;
}

/** @var class-string<\Webmaster\Core> $core */
[$entrypoint, $core] = require $_SERVER['SCRIPT_FILENAME'];

$core = new $core();

/** @var \Webmaster\Entrypoint\EntrypointInterface $entrypoint */
$entrypoint = $core->getContainer()->get($entrypoint);

$entrypoint->setCore($core);

exit($entrypoint->handle());
