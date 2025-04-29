<?php

namespace Domain\Members\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;

class UpdateStatusMemberAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param $memberId
     * @param $activated
     * @return true
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute($memberId, $activated): bool
    {
        $activated = $this->memberRepository->updateStatus($memberId, $activated);

        if($activated)
        {
            return true;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_STATUS_MEMBER, 500);
        }
    }
}
