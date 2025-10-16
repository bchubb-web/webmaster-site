<?php

use League\Config\Configuration;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

return function (): Configuration {
    $config = new Configuration();

    $config->addSchema('view', viewSchema());
    $config->addSchema('http', httpSchema());
    $config->addSchema('db', dbSchema());

    return $config;
};

function viewSchema(): Schema
{
    return Expect::structure([
        'load_from' => Expect::array([])->required(),
    ]);
}

function httpSchema(): Schema
{
    return Expect::structure([
        'base_uri' => Expect::string()->required(), 
        'redirects' => Expect::string('redirects.ini'),
    ]);
}

function dbSchema(): Schema
{
    return Expect::structure([
        'connection' => Expect::array([])->required(),
    ]);
}
