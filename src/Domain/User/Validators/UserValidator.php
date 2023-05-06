<?php

declare(strict_types=1);

namespace App\Domain\User\Validators;

use App\Domain\User\User;
use Respect\Validation\Exceptions\ValidatorException;
use Respect\Validation\Validator as Validator;

class UserValidator implements UserValidatorInterface
{
    /**
     * @param array $data
     * @throws ValidatorException
     * @return void
     */
    public static function validate(array $data): void
    {
        $validator = Validator::key(User::FIRST_NAME, Validator::stringType()->notEmpty())
            ->key(User::LAST_NAME, Validator::stringType()->notEmpty())
            ->key(User::PESEL, Validator::pesel()->notEmpty())
            ->key(User::EMAIL, Validator::email()->notEmpty())
            ->key(User::CONTACT_EMAILS, Validator::arrayType()->each(Validator::email()), false);

        $validator->assert($data);
    }
}
