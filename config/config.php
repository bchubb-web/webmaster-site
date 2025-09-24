<?php

use League\Config\Configuration;

return function (Configuration $config): Configuration {
    $config->merge([
        'http' => [
            'base_uri' => 'http://site.webmaster.orb.local/',
        ],
        'view' => [
            'load_from' => [
                __DIR__ . '/../views',
            ],
        ],
    ]);

    return $config;
};

