<?php

namespace Domain\Users\Interfaces;

use Domain\Users\DataTransferObjects\UserData;
use Illuminate\Support\Collection;
use Domain\Users\Models\User;

interface UserRepositoryInterface
{
    public function createUser(UserData $userData): User;
}
