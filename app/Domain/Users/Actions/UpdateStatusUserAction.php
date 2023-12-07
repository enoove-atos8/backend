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

class UpdateStatusUserAction
{
    private MemberRepository $userRepository;

    public function __construct(MemberRepositoryInterface $userRepositoryInterface)
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
