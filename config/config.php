<?php

use League\Config\Configuration;

return function (Configuration $config): Configuration {
    $config->merge([
        'view' => [
            'load_from' => [
                __DIR__ . '/../views',
            ],
        ],
    ]);

    return $config;
};

