<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    public function findByEmailOrPesel(string $email, string $pesel): User|false;
    public function findById(int $id): User|false;
    public function update(User $user): int;
    public function create(User $user): User;
}
