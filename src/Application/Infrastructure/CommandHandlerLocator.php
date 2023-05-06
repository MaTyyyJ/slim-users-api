<?php

namespace App\Application\Infrastructure;

use Exception;
use Psr\Container\ContainerInterface;
use League\Tactician\Handler\Locator\HandlerLocator;

class CommandHandlerLocator implements HandlerLocator
{
    private ContainerInterface $container;
    private array $handlerMap;

    public function __construct(ContainerInterface $container, array $handlerMap)
    {
        $this->container = $container;
        $this->handlerMap = $handlerMap;
    }

    public function getHandlerForCommand($commandName)
    {
        if (isset($this->handlerMap[$commandName])) {
            return $this->container->get($this->handlerMap[$commandName]);
        }

        throw new Exception('No handler found for command: ' . $commandName);
    }
}