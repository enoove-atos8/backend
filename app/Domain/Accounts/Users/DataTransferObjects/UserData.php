<?php

namespace App\Domain\Accounts\Users\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UserData extends DataTransferObject
{
    /** @var integer  */
    public int $id = 0;

    /** @var string  */
    public string $email;

    /** @var string|null  */
    public string|null $password;

    /** @var boolean  */
    public bool $activated;

    /** @var string  */
    public string $type;

    /** @var boolean  */
    public bool $changedPassword;

    /** @var string  */
    public string $accessQuantity;

    /** @var array  */
    public array $roles;
}
