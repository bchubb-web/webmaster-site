<?php

declare(strict_types=1);

define('ROOT', dirname(__DIR__, 1));

define('WEBMASTER', ROOT . '/src');

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
