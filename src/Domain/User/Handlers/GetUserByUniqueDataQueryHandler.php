<?php

declare(strict_types=1);

namespace App\Domain\User\Handlers;

use App\Domain\User\Queries\GetUserByUniqueDataQuery;
use App\Domain\User\User;

class GetUserByUniqueDataQueryHandler extends Handler
{
    public function handle(GetUserByUniqueDataQuery $query): User|false
    {
        $email = $query->getEmail();
        $pesel = $query->getPesel();
        return $this->userRepository->findByEmailOrPesel($email, $pesel);
    }
}
