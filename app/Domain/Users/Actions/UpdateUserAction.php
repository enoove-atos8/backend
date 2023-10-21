<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Interfaces\UserDetailRepositoryInterface;
use Domain\Users\Models\UserDetail;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserDetailRepository;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

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
    public function __invoke($id, UserData $userData, UserDetailData $userDetailData): User
    {
        $this->userRepository->updateUser($id, $userData);
        $this->updateUserDetailAction->__invoke($id, $userDetailData);

        $user = $this->userRepository->getUsers($id);

        $user->syncRoles($userData->roles);



        // Call action here that handle email to user activate your account

        return $user;
    }
}
