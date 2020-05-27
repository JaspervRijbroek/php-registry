<?php

return [
    'root' => __DIR__,
    'cache' => [
        'path' => __DIR__ . '/storage/cache'
    ],
    'db' => [
        'file' => __DIR__ . '/storage/db/database.db',
        'migrations' => [
            'path' => __DIR__ . '/storage/db/migrations'
        ]
    ],
    'npm' => [
        'upstream' => 'https://registry.npmjs.org'
    ],
    'upload' => [
        'path' => __DIR__ . '/storage/packages'
    ]
];