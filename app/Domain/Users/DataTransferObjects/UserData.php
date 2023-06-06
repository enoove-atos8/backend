<?php

namespace Domain\Users\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UserData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $email;

    /** @var string  */
    public string $password;

    /** @var boolean  */
    public bool $activated;

    /** @var string  */
    public string $type;
}
