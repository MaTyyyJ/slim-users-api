<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Application\Utils\QueryBuilder;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Exception;
use PDO;

class PostgresUserRepository implements UserRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function update(User $user): int
    {
        $stmt = $this->db->prepare('
        UPDATE public.users
        SET first_name=:firstName, last_name=:lastName, pesel=:pesel, email=:email, contact_emails=:contactEmails
        WHERE id=:id;
        ');
        $stmt->bindValue(':id', $user->getId());
        $stmt->bindValue(':firstName', $user->getFirstName());
        $stmt->bindValue(':lastName', $user->getLastName());
        $stmt->bindValue(':pesel', $user->getPesel());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':contactEmails', $user->getContactEmails());

        $executeQuery = $stmt->execute();
        if ($executeQuery) {
            return $stmt->rowCount();
        } else {
            throw new Exception('Error during user update.');
        }
    }

    public function create(User $user): User
    {
        $stmt = $this->db->prepare('
        INSERT INTO users 
            (first_name, last_name, pesel, email, contact_emails) 
            VALUES (:firstName, :lastName, :pesel, :email, :contactEmails)');
        $stmt->bindValue(':firstName', $user->getFirstName());
        $stmt->bindValue(':lastName', $user->getLastName());
        $stmt->bindValue(':pesel', $user->getPesel());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':contactEmails', $user->getContactEmails());

        if (!$stmt->execute()) {
            throw new Exception('Error during user creation.');
        }
        $user->setId((int)$this->db->lastInsertId());
        return $user;
    }

    public function findByEmailOrPesel(string $email, string $pesel): User|false
    {
        $sth = $this->db->prepare('SELECT * FROM users WHERE "email" = :email or "pesel"= :pesel');
        $sth->bindValue(':email', $email);
        $sth->bindValue(':pesel', $pesel);
        $sth->execute();

        return $sth->fetchObject(User::class);
    }


    public function findById(int $id): User|false
    {
        $sth = $this->db->prepare('SELECT * FROM users WHERE "id" = :id');
        $sth->bindValue(':id', $id);
        $sth->execute();

        return $sth->fetchObject(User::class);
    }
}
