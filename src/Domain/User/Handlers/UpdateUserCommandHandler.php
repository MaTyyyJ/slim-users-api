<?php

declare(strict_types=1);

namespace App\Domain\User\Handlers;

use App\Domain\User\Commands\UpdateUserCommand;
use App\Domain\User\Exceptions\UserAlreadyExistsException;
use App\Domain\User\Queries\GetUserByUniqueDataQuery;
use App\Domain\User\User;
use App\Domain\User\Validators\UserValidator;

class UpdateUserCommandHandler extends Handler
{
    public function handle(UpdateUserCommand $command): int
    {
        $user = $command->getUser();
        UserValidator::validate($command->getUser()->jsonSerialize());
        $query = new GetUserByUniqueDataQuery($user->getEmail(), $user->getPesel());
        /**
         * @var User|false $userExists
         */
        $userExists = $this->commandBus->dispatch($query);
        if ($userExists && $userExists->getId() !== $user->getId()) {
            throw new UserAlreadyExistsException();
        }

        return $this->userRepository->update($user);
    }
}
