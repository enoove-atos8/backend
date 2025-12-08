<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;

class BatchCreateMembersAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(
        MemberRepositoryInterface $memberRepositoryInterface,
    ) {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param  \Domain\Secretary\Membership\DataTransferObjects\MemberData[]  $membersData
     */
    public function execute(array $membersData): bool
    {
        return $this->memberRepository->batchCreateMembers($membersData);
    }
}
