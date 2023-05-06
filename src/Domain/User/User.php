<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Validators\UserValidator;
use JsonSerializable;

class User implements JsonSerializable
{
    public const ID = 'id';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const PESEL = 'pesel';
    public const EMAIL = 'email';
    public const CONTACT_EMAILS = 'contact_emails';
    private int $id;
    private string $email;
    private string $first_name;
    private string $last_name;
    private string $pesel;
    private string $contact_emails;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): int
    {
        return $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }

    public function getPesel(): string
    {
        return $this->pesel;
    }

    public function setPesel(string $pesel): void
    {
        $this->pesel = $pesel;
    }

    public function getContactEmails(): string
    {
        return $this->contact_emails;
    }

    public function setContactEmails(string $contactEmails): void
    {
        $this->contact_emails = $contactEmails;
    }

    public static function createFromArray(array $userData): User
    {
        UserValidator::validate($userData);
        $user = new self();
        $user->setEmail($userData[User::EMAIL]);
        $user->setFirstName($userData[User::FIRST_NAME]);
        $user->setLastName($userData[User::LAST_NAME]);
        $user->setPesel($userData[User::PESEL]);

        $contactEmails = $userData[User::CONTACT_EMAILS] ?? [];
        $user->setContactEmails(json_encode($contactEmails));
        return $user;
    }

    public function setData(string $propertyName, $value): void
    {
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = $value;
        }
    }

    public function jsonSerialize(): array
    {
        return [
            User::ID  => $this->id ?? 0,
            User::FIRST_NAME => $this->first_name,
            User::LAST_NAME => $this->last_name,
            User::EMAIL => $this->email,
            User::PESEL => $this->pesel,
            User::CONTACT_EMAILS => json_decode($this->contact_emails)
        ];
    }
}
