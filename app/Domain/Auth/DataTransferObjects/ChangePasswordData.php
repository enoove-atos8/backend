<?php

namespace App\Domain\Auth\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class ChangePasswordData extends DataTransferObject
{
    /** @var string */
    public string $currentPassword;

    /** @var string */
    public string $newPassword;
}
