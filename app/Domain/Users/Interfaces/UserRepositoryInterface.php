<?php

namespace Domain\Users\Interfaces;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\SubDomains\Roles\Models\Role;
use Illuminate\Support\Collection;
use Domain\Users\Models\User;

interface UserRepositoryInterface
{
    public function all(): Collection;

    public function createUser(UserData $userData): User;

    public function attachRoles(UserData $userData, User $user): void;

    public function attachAbilities(UserData $userData, User $user): void;
}
