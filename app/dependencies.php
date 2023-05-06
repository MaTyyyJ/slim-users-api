<?php

declare(strict_types=1);

use App\Application\Infrastructure\AMQPClient;
use App\Application\Infrastructure\CommandBus;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        PDO::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');

            $driver = $dbSettings['driver'];
            $host = $dbSettings['host'];
            $dbname = $dbSettings['database'];
            $username = $dbSettings['username'];
            $password = $dbSettings['password'];
            $dsn = "$driver:host=$host;dbname=$dbname";

            return new PDO($dsn, $username, $password);
        },
        CommandBus::class => function (ContainerInterface $container) {
            return new CommandBus($container);
        },
        AMQPStreamConnection::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);
            $amqpSettings = $settings->get('amqp')['rabbitmq'];
            return new AMQPStreamConnection(
                $amqpSettings['host'],
                $amqpSettings['port'],
                $amqpSettings['username'],
                $amqpSettings['password'],
                $amqpSettings['vhost']
            );
        },
    ]);
};
