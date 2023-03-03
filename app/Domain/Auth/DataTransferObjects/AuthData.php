<?php

namespace Domain\Auth\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class AuthData extends DataTransferObject
{

    /** @var string  */
    public string $email;

    /** @var string  */
    public string $password;

}
