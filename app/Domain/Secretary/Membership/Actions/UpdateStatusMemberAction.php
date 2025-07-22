<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

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
