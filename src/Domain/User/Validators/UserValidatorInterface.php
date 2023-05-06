<?php

declare(strict_types=1);

namespace App\Domain\User\Validators;

use Respect\Validation\Exceptions\ValidatorException;

interface UserValidatorInterface
{
    /**
     * @param array $data
     * @throws ValidatorException
     * @return void
     */
    public static function validate(array $data): void;
}
