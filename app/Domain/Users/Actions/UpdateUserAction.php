<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Interfaces\MemberDetailRepositoryInterface;
use Domain\Users\Models\UserDetail;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberDetailRepository;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Users\DataTransferObjects\MemberData;
use Domain\Users\Interfaces\MemberRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class UpdateUserAction
{
    private MemberRepository $userRepository;
    private UpdateUserDetailAction $updateUserDetailAction;

    public function __construct(MemberRepositoryInterface $userRepositoryInterface, UpdateUserDetailAction $updateUserDetailAction)
    {
        $this->userRepository = $userRepositoryInterface;
        $this->updateUserDetailAction = $updateUserDetailAction;
    }

    /**
     * @param $id
     * @param MemberData $userData
     * @param UserDetailData $userDetailData
     * @return User
     * @throws BindingResolutionException
     */
    public function __invoke($id, MemberData $userData, UserDetailData $userDetailData): User
    {
        $this->userRepository->updateUser($id, $userData);
        $this->updateUserDetailAction->__invoke($id, $userDetailData);

        $user = $this->userRepository->getUsers($id);

        $user->syncRoles($userData->roles);



        // Call action here that handle email to user activate your account

        return $user;
    }
}
