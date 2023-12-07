<?php

namespace Domain\Members\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberDetailRepository;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Throwable;

class UpdateStatusMemberAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param $memberId
     * @param $status
     * @return int
     * @throws BindingResolutionException
     */
    public function __invoke($memberId, $status): int
    {
        return $this->memberRepository->updateStatus($memberId, $status);
    }
}
