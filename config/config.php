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
        'db' => [
            'connection' => [
                'driver' => 'pdo_mysql',
                'host' => 'db',
                'dbname' => 'app_db',
                'user' => 'app_user',
                'password' => 'app_password',
            ],
        ],
    ]);

    return $config;
};

