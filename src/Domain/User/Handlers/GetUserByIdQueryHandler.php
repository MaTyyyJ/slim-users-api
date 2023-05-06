<?php

declare(strict_types=1);

namespace App\Domain\User\Handlers;

use App\Domain\User\Queries\GetUserByIdQuery;
use App\Domain\User\User;

class GetUserByIdQueryHandler extends Handler
{
    public function handle(GetUserByIdQuery $query): User|bool
    {
        return $this->userRepository->findById($query->getId());
    }
}
