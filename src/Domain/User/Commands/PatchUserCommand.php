<?php

declare(strict_types=1);

namespace App\Domain\User\Commands;

use App\Domain\User\User;

class PatchUserCommand
{
    private User $user;
    private array $userChangedData;

    /**
     * @param User $user
     * @param array $userData
     */
    public function __construct(
        User $user,
        array $userData
    ) {
        $this->user = $user;
        $this->userChangedData = $userData;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserChangedData(): array
    {
        return $this->userChangedData;
    }
}
