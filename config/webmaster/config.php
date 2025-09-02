<?php

use League\Config\Configuration;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

return function (): Configuration {
    $config = new Configuration();

    $config->addSchema('view', viewSchema());

    return $config;
};

function viewSchema(): Schema
{
    return Expect::structure([
        'load_from' => Expect::array([])->required(),
    ]);
}
