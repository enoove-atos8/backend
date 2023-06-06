<?php

namespace Infrastructure\Repositories\User;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected mixed $model = User::class;

    public function createUser(UserData $userData): User
    {
        $test = $userData;
    }
}
