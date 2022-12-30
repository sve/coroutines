<?php

use Monolog\Level;
use Monolog\Handler\StreamHandler;

return function() {
    return [
        'log' => [
            'errors' => true,
            'details' => false,
        ],
        'pipes' => [
            'stdout' => false,
            'stderr' => false,
            StreamHandler::class => true,
        ],
        'logger' => [
            'name' => 'application',
            'handlers' => [
                'stdout' => [
                    'path' => 'php://stdout',
                    'level' => Level::Error->value,
                ],
                'stderr' => [
                    'path' => 'php://stderr',
                    'level' => Level::Debug->value,
                ],
                StreamHandler::class => [
                    'path' => __DIR__ . '/../logs/application.log',
                    'level' => Level::Debug->value,
                ],
            ],
        ],
    ];
};