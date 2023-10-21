<?php

namespace Domain\Users\Interfaces;

use Domain\Users\DataTransferObjects\UserData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Domain\Users\Models\User;

interface UserRepositoryInterface
{
    public function createUser(UserData $userData): User;

    public function getUsers(): User|Collection;

    public function updateStatus($id, $status): int;

    public function updateUser($id, UserData $userData): int;
}
