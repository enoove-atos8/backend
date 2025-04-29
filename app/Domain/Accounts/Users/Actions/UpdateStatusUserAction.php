<?php

namespace App\Domain\Accounts\Users\Actions;


use App\Domain\Accounts\Users\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Repositories\Accounts\User\UserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

class UpdateStatusUserAction
{
    private UserRepositoryInterface $userRepository;

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
    public function execute($userId, $status): int
    {
        return $this->userRepository->updateStatus($userId, $status);
    }
}
