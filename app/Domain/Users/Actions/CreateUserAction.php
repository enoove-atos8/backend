<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Users\DataTransferObjects\MemberData;
use Domain\Users\Interfaces\MemberRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class CreateUserAction
{
    private MemberRepository $userRepository;
    private CreateUserDetailAction $createUserDetailAction;

    public function __construct(
        MemberRepositoryInterface $userRepositoryInterface,
        CreateUserDetailAction    $createUserDetailAction,
    )
    {
        $this->userRepository = $userRepositoryInterface;
        $this->createUserDetailAction = $createUserDetailAction;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(MemberData $userData, UserDetailData $userDetailData): User
    {
        $user = $this->userRepository->createUser($userData);
        $this->createUserDetailAction->__invoke($user->id, $userDetailData);

        $user->assignRole($userData->roles);

        // Call action here that handle email to user activate your account

        return $user;
    }
}
