<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Queries\GetUserByIdQuery;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;

class GetUserAction extends UserAction
{
    /**
     * @throws UserNotFoundException
     */
    protected function action(): Response
    {
        $userId = (int)$this->resolveArg('id');
        $query = new GetUserByIdQuery($userId);
        $userExists = $this->commandBus->dispatch($query);
        if (!$userExists) {
            throw new UserNotFoundException();
        }
        return $this->respondWithData($userExists, StatusCodeInterface::STATUS_OK);
    }
}
