<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Monolog\Logger;

$dotenv = Dotenv::createImmutable(Settings::getAppRoot());
$dotenv->load();

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'amqp' => [
                    'rabbitmq' => [
                        'host' => $_ENV['RABBITMQ_HOST'],
                        'port' => $_ENV['RABBITMQ_PORT'],
                        'username' => $_ENV['RABBITMQ_USER'],
                        'password' => $_ENV['RABBITMQ_PASS'],
                        'vhost' => $_ENV['RABBITMQ_VHOST'],
                    ],
                ],
                'db' => [
                    'driver' => $_ENV['DATABASE_DRIVER'],
                    'host' => $_ENV['DATABASE_HOST'],
                    'username' => $_ENV['DATABASE_USER'],
                    'database' => $_ENV['DATABASE_NAME'],
                    'password' => $_ENV['DATABASE_PASSWORD'],
                ],
                'displayErrorDetails' => (bool)$_ENV['DISPLAY_ERROR_DETAILS'],
                'logError' => (bool)$_ENV['LOG_ERRORS'],
                'logErrorDetails' => (bool)$_ENV['LOG_ERROR_DETAILS'],
                'logger' => [
                    'name' => $_ENV['LOG_APP_NAME'],
                    'path' => __DIR__.'/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
            ]);
        },
    ]);
};
