<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Commands\CreateUserCommand;
use App\Domain\User\User;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Exceptions\ValidatorException;

class CreateUserAction extends UserAction
{
    protected function action(): Response
    {
        $data = $this->jsonRequestBody;

        $user = User::createFromArray($data);
        $command = new CreateUserCommand($user);
        try {
            $createdUser = $this->commandBus->dispatch($command);
        } catch (ValidatorException $validatorException) {
            return $this->respondWithData(
                ['errors' => $validatorException->getMessages()],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
        return $this->respondWithData([User::ID => $createdUser->getId()], StatusCodeInterface::STATUS_CREATED);
    }
}
