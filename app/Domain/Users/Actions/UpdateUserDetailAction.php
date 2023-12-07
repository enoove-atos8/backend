<?php

namespace Domain\Users\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Domain\Users\Interfaces\MemberDetailRepositoryInterface;
use Domain\Users\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberDetailRepository;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Users\DataTransferObjects\MemberData;
use Domain\Users\Interfaces\MemberRepositoryInterface;
use Domain\Users\Models\User;
use Throwable;

class UpdateUserDetailAction
{
    private MemberDetailRepository $userDetailRepository;

    public function __construct(MemberDetailRepositoryInterface $userDetailRepositoryInterface)
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
