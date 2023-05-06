<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Commands\PatchUserCommand;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Queries\GetUserByIdQuery;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Exceptions\ValidatorException;

class PatchUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->jsonRequestBody;

        $userId = (int)$this->resolveArg('id');
        $query = new GetUserByIdQuery($userId);
        $userExists = $this->commandBus->dispatch($query);
        if (!$userExists) {
            throw new UserNotFoundException();
        }

        $command = new PatchUserCommand($userExists, $data);
        try {
            $updatedCount = $this->commandBus->dispatch($command);
        } catch (ValidatorException $validatorException) {
            return $this->respondWithData(
                ['errors' => $validatorException->getMessages()],
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
        $statusCode = $updatedCount > 0 ? StatusCodeInterface::STATUS_OK : StatusCodeInterface::STATUS_NOT_MODIFIED;
        return $this->respondWithData(['updated' => $updatedCount], $statusCode);
    }
}
