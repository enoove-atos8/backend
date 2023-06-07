<?php

namespace Infrastructure\Repositories\User;

use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected mixed $model = User::class;

    /**
     * @throws \Throwable
     */
    public function createUser(UserData $userData): User
    {
        $user = $this->create([
            'email'         =>  $userData->email,
            'password'      =>  bcrypt($userData->password),
            'activated'     =>  $userData->activated,
            'type'          =>  $userData->type,
        ]);

        throw_if(!$user, GeneralExceptions::class, 'Houve um erro ao procesar o cadastro de uma nova igreja', 500);

        return $user;
    }
}
