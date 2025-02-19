<?php

namespace App\Domain\Accounts\Users\Actions;


use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use App\Domain\Accounts\Users\Interfaces\UserDetailRepositoryInterface;
use App\Infrastructure\Repositories\Accounts\User\UserDetailRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

class UpdateUserDetailAction
{
    private UserDetailRepository $userDetailRepository;

    public function __construct(UserDetailRepositoryInterface $userDetailRepositoryInterface)
    {
        $this->userDetailRepository = $userDetailRepositoryInterface;
    }

    /**
     * @param $id
     * @param UserDetailData $userDetailData
     * @return int
     * @throws BindingResolutionException
     */
    public function execute($id, UserDetailData $userDetailData): int
    {
        return $this->userDetailRepository->updateUserDetail($id, $userDetailData);
    }
}
