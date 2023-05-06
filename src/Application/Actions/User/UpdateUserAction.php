<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Commands\CreateUserCommand;
use App\Domain\User\Commands\UpdateUserCommand;
use App\Domain\User\Queries\GetUserByIdQuery;
use App\Domain\User\User;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Exceptions\ValidatorException;

class UpdateUserAction extends UserAction
{
    protected function action(): Response
    {
        $data = $this->jsonRequestBody;

        $userId = (int)$this->resolveArg('id');
        $query = new GetUserByIdQuery($userId);
        /** @var User $userExists */
        $userExists = $this->commandBus->dispatch($query);
        try {
            $user = User::createFromArray($data);
            if ($userExists) {
                $user->setId($userExists->getId());
                $command = new UpdateUserCommand($user);
                $updatedCount = $this->commandBus->dispatch($command);
                return $this->respondWithData(['updated' => $updatedCount], StatusCodeInterface::STATUS_OK);
            }
            $command = new CreateUserCommand($user);
            $user = $this->commandBus->dispatch($command);
            return $this->respondWithData([User::ID => $user->getId()], StatusCodeInterface::STATUS_CREATED);
        } catch (ValidatorException $validatorException) {
            return $this->respondWithData(
                ['errors' => $validatorException->getMessages()],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }
}
