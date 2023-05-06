<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Infrastructure\CommandBus;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;
    protected CommandBus $commandBus;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository, CommandBus $commandBus)
    {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
        $this->commandBus = $commandBus;
    }
}
