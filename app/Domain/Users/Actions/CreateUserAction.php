<?php

namespace Domain\Users\Actions;

use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;

class CreateUserAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }

    public function __invoke(UserData $userData): User
    {
        $user = $this->userRepository->createUser($userData);




        return $user;
    }

}
