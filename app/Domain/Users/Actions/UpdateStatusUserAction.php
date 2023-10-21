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

class UpdateStatusUserAction
{
    private UserRepository $userRepository;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }

    /**
     * @param $userId
     * @param $status
     * @return int
     * @throws BindingResolutionException
     */
    public function __invoke($userId, $status): int
    {
        return $this->userRepository->updateStatus($userId, $status);
    }
}
