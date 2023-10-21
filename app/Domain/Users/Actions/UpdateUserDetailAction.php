<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Interfaces\UserDetailRepositoryInterface;
use Domain\Users\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\UserDetailRepository;
use Infrastructure\Repositories\User\UserRepository;
use Domain\Users\DataTransferObjects\UserData;
use Domain\Users\Interfaces\UserRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

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
     */
    public function __invoke($id, UserDetailData $userDetailData): int
    {
        return $this->userDetailRepository->updateUserDetail($id, $userDetailData);
    }
}
