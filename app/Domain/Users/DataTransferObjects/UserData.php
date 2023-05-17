<?php

namespace Domain\Users\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UserData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $name;

    /** @var string  */
    public string $email;

    /** @var string  */
    public string $password;

    /** @var string  */
    public string $type;

    /** @var array  */
    public array $roles;

}
