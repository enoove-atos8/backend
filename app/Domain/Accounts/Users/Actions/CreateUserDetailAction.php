<?php

namespace App\Domain\Accounts\Users\Actions;


use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Domain\Accounts\Users\Models\UserDetail;
use App\Infrastructure\Repositories\Users\User\UserDetailRepository;
use Throwable;

class CreateUserDetailAction
{
    private UserDetailRepositoryInterface $userDetailRepository;

    public function __construct(
        UserDetailRepositoryInterface $userDetailRepositoryInterface,
    )
    {
        $this->userDetailRepository = $userDetailRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute($userId, UserDetailData $userDetailData): UserDetail
    {
        return $this->userDetailRepository->createUserDetail($userId, $userDetailData);
    }
}
