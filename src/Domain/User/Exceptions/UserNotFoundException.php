<?php

declare(strict_types=1);

namespace App\Domain\User\Exceptions;

use App\Domain\DomainException\DomainException;

class UserNotFoundException extends DomainException
{
    public $code = 404;
    public $message = 'The user you requested does not exist.';
}
