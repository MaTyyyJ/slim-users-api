<?php

declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\DomainException\DomainException;

class UserAlreadyExistsException extends DomainException
{
    protected $code = 409;
    public $message = 'The user with the specified PESEL number or e-mail address already exists';
}
