<?php

declare(strict_types=1);

namespace App\Domain\User\Validators;

use App\Domain\User\User;
use Respect\Validation\Validator as Validator;

class PatchUserValidator implements UserValidatorInterface
{
    public static function validate(array $data): void
    {
        $validator = Validator::key(User::FIRST_NAME, Validator::stringType(), false)
            ->key(User::LAST_NAME, Validator::stringType(), false)
            ->key(User::PESEL, Validator::pesel()->notEmpty(), false)
            ->key(User::EMAIL, Validator::email()->notEmpty(), false)
            ->key(User::CONTACT_EMAILS, Validator::arrayType()->each(Validator::email()), false);

        $validator->assert($data);
    }
}
