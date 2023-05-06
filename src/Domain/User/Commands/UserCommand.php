<?php

declare(strict_types=1);

namespace App\Domain\User\Commands;

use App\Domain\User\User;

class UserCommand
{
    private User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
