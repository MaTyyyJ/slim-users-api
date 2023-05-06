<?php

declare(strict_types=1);

namespace App\Domain\User\Queries;

class GetUserByUniqueDataQuery
{
    private string $email;
    private string $pesel;

    public function __construct(string $email, string $pesel)
    {
        $this->email = $email;
        $this->pesel = $pesel;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPesel(): string
    {
        return $this->pesel;
    }
}
