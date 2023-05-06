<?php

namespace App\Domain\User\Handlers;

use App\Application\Infrastructure\CommandBus;
use App\Domain\User\UserRepository;

abstract class Handler
{
    protected UserRepository $userRepository;
    protected CommandBus $commandBus;

    public function __construct(UserRepository $userRepository, CommandBus $commandBus)
    {
        $this->userRepository = $userRepository;
        $this->commandBus = $commandBus;
    }
}
