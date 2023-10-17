<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class CreateUserAction
{
    private UserRepository $userRepository;
    private CreateUserDetailAction $createUserDetailAction;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, CreateUserDetailAction $createUserDetailAction)
    {
        $this->userRepository = $userRepositoryInterface;
        $this->createUserDetailAction = $createUserDetailAction;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(UserData $userData, UserDetailData $userDetailData): User
    {
        $user = $this->userRepository->createUser($userData);
        $this->createUserDetailAction->__invoke($user->id, $userDetailData);

        $user->assignRole($userData->roles);

        // Call action here that handle email to user activate your account

        return $user;
    }
}
