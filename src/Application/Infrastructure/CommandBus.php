<?php

namespace App\Application\Infrastructure;

use App\Domain\User\Commands\CreateUserCommand;
use App\Domain\User\Commands\PatchUserCommand;
use App\Domain\User\Commands\UpdateUserCommand;
use App\Domain\User\Handlers\GetUserByIdQueryHandler;
use App\Domain\User\Handlers\CreateUserCommandHandler;
use App\Domain\User\Handlers\GetUserByUniqueDataQueryHandler;
use App\Domain\User\Handlers\PatchUserCommandHandler;
use App\Domain\User\Handlers\UpdateUserCommandHandler;
use App\Domain\User\Queries\GetUserByIdQuery;
use App\Domain\User\Queries\GetUserByUniqueDataQuery;
use League\Tactician\CommandBus as TacticianCommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Psr\Container\ContainerInterface;

class CommandBus
{
    private TacticianCommandBus $commandBus;

    public function __construct(ContainerInterface $container)
    {
        $handlerMap = [
            CreateUserCommand::class => CreateUserCommandHandler::class,
            UpdateUserCommand::class => UpdateUserCommandHandler::class,
            PatchUserCommand::class => PatchUserCommandHandler::class,
            GetUserByIdQuery::class => GetUserByIdQueryHandler::class,
            GetUserByUniqueDataQuery::class => GetUserByUniqueDataQueryHandler::class
        ];

        $handlerLocator = new CommandHandlerLocator($container, $handlerMap);

        $handlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            $handlerLocator,
            new HandleInflector()
        );

        $this->commandBus = new TacticianCommandBus([$handlerMiddleware]);
    }

    public function dispatch($command)
    {
        return $this->commandBus->handle($command);
    }
}
