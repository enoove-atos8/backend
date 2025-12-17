<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;

class BatchCreateMembersAction
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
        private SyncMemberCountAction $syncMemberCountAction
    ) {}

    /**
     * @param  \Domain\Secretary\Membership\DataTransferObjects\MemberData[]  $membersData
     */
    public function execute(array $membersData): bool
    {
        $result = $this->memberRepository->batchCreateMembers($membersData);

        if ($result) {
            $this->syncMemberCountAction->execute();
        }

        return $result;
    }
}
