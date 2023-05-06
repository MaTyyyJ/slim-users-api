<?php

declare(strict_types=1);

namespace App\Domain\User\Handlers;

use App\Domain\User\Commands\PatchUserCommand;
use App\Domain\User\Exceptions\UserAlreadyExistsException;
use App\Domain\User\Queries\GetUserByUniqueDataQuery;
use App\Domain\User\User;
use App\Domain\User\Validators\PatchUserValidator;

class PatchUserCommandHandler extends Handler
{
    /**
     * @throws UserAlreadyExistsException
     */
    public function handle(PatchUserCommand $command): int
    {
        PatchUserValidator::validate($command->getUserChangedData());
        $user = $command->getUser();
        $originalUser = clone $user;
        $userArray = $user->jsonSerialize();
        $changedData = [];
        foreach ($command->getUserChangedData() as $key => $value) {
            if (isset($userArray[$key]) && $userArray[$key] !== $value) {
                if ($key === "contact_emails") {
                    $value = json_encode($value);
                }
                $user->setData($key, $value);
                $changedData[$key] = $value;
            }
        }

        if ($user == $originalUser) {
            return 0;
        }

        if (array_key_exists(User::PESEL, $changedData) || array_key_exists(User::EMAIL, $changedData)) {
            $query = new GetUserByUniqueDataQuery($changedData[User::EMAIL] ?? "", $changedData[User::PESEL] ?? "");
            $userExists = $this->commandBus->dispatch($query);
            if ($userExists && $userExists->getId() !== $user->getId()) {
                throw new UserAlreadyExistsException();
            }
        }

        return $this->userRepository->update($user);
    }
}
