<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Domain\Secretary\Membership\Models\Member;
use Throwable;

class CreateMemberAction
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
        private SyncMemberCountAction $syncMemberCountAction
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(MemberData $memberData): Member
    {
        $member = $this->memberRepository->createMember($memberData);

        $this->syncMemberCountAction->execute();

        return $member;
    }
}
