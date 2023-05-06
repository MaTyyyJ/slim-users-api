<?php

declare(strict_types=1);

namespace App\Domain\User\Handlers;

use App\Domain\User\Commands\CreateUserCommand;
use App\Domain\User\Exceptions\UserAlreadyExistsException;
use App\Domain\User\Queries\GetUserByUniqueDataQuery;
use App\Domain\User\User;
use App\Domain\User\Validators\UserValidator;

class CreateUserCommandHandler extends Handler
{
    /**
     * @throws UserAlreadyExistsException
     */
    public function handle(CreateUserCommand $command): User
    {
        UserValidator::validate($command->getUser()->jsonSerialize());

        $user = $command->getUser();
        $query = new GetUserByUniqueDataQuery($user->getEmail(), $user->getPesel());
        $userExists = $this->commandBus->dispatch($query);
        if ($userExists) {
            throw new UserAlreadyExistsException();
        }

        return $this->userRepository->create($user);
    }
}
