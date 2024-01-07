<?php

namespace Domain\Members\Actions;

use App\Domain\Members\Constants\ReturnMessages;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\Interfaces\MemberRepositoryInterface;

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
     * @return true
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke($memberId, $status): bool
    {
        $status = $this->memberRepository->updateStatus($memberId, $status);

        if($status)
        {
            return true;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_STATUS_MEMBER, 500);
        }
    }
}
