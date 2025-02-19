<?php

namespace App\Domain\Accounts\Users\Actions;


use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Domain\Accounts\Users\Models\User;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

class UpdateUserAction
{
    private UserRepository $userRepository;
    private UpdateUserDetailAction $updateUserDetailAction;

    public function __construct(UserRepositoryInterface $userRepositoryInterface, UpdateUserDetailAction $updateUserDetailAction)
    {
        $this->userRepository = $userRepositoryInterface;
        $this->updateUserDetailAction = $updateUserDetailAction;
    }

    /**
     * @param $id
     * @param UserData $userData
     * @param UserDetailData $userDetailData
     * @return User
     * @throws BindingResolutionException
     */
    public function execute($id, UserData $userData, UserDetailData $userDetailData): User
    {
        $this->userRepository->updateUser($id, $userData);
        $this->updateUserDetailAction->execute($id, $userDetailData);

        $user = $this->userRepository->getUsers($id);

        $user->syncRoles($userData->roles);



        // Call action here that handle email to user activate your account

        return $user;
    }
}
