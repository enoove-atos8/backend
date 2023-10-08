<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Interfaces\UserDetailRepositoryInterface;
use Domain\Users\Models\UserDetail;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserDetailRepository;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class CreateUserDetailAction
{
    private UserRepository $userRepository;
    private UserDetailRepository $userDetailRepository;

    public function __construct(
        UserRepositoryInterface $userRepositoryInterface,
        UserDetailRepositoryInterface $userDetailRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
        $this->userDetailRepository = $userDetailRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function __invoke($userId, UserDetailData $userDetailData): UserDetail
    {
        return $this->userDetailRepository->createUserDetail($userId, $userDetailData);
    }
}
