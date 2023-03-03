<?php

namespace Domain\Users\Actions;

use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;

class CreateUserAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserData $userData): User
    {
        $user = $this->userRepository->createUser($userData);

        if (count($userData->roles) > 0){

            $userData->id = $user->id;
            //$this->user->roles()->attach($userData->roles["role_id"]);
            //$role = $this->user->roles()->first();
            //$role->abilities()->attach($userData->roles["abilities"]);
        }


        return $user;
    }
}
