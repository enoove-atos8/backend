<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Interfaces\MemberDetailRepositoryInterface;
use Domain\Users\Models\UserDetail;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberDetailRepository;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Users\DataTransferObjects\MemberData;
use Domain\Users\Interfaces\MemberRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class CreateUserDetailAction
{
    private MemberRepository $userRepository;
    private MemberDetailRepository $userDetailRepository;

    public function __construct(
        MemberRepositoryInterface       $userRepositoryInterface,
        MemberDetailRepositoryInterface $userDetailRepositoryInterface,
    )
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
