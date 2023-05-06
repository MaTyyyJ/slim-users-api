<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
    'migration_dirs' => [
        'phoenix' => __DIR__.'/database',
    ],
    'environments' => [
        'pgsql' => [
            'adapter' => $_ENV['DATABASE_DRIVER'],
            'host' => $_ENV['DATABASE_HOST'],
            'username' => $_ENV['DATABASE_USER'],
            'db_name' => $_ENV['DATABASE_NAME'],
            'password' => $_ENV['DATABASE_PASSWORD'],
        ],
    ],
];
