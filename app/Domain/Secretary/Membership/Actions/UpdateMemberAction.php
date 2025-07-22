<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateMemberAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param $id
     * @param MemberData $memberData
     * @return true
     * @throws GeneralExceptions
     */
    public function execute($id, MemberData $memberData): bool
    {
        if($this->memberRepository->updateMember($id, $memberData))
        {
            return true;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_MEMBER, 500);
        }
    }
}
