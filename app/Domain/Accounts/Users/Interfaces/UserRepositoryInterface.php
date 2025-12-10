<?php

namespace App\Domain\Accounts\Users\Interfaces;


use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function createUser(UserData $userData): User;

    public function getUsers(): User|Collection;

    public function updateStatus($id, $status): int;

    public function updateUser($id, UserData $userData): int;

    public function changePassword(int $userId, string $newPassword): bool;

    public function deleteUser(int $id): bool;
}
