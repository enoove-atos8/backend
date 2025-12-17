<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;

class CountActiveMembersAction
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository
    ) {}

    public function execute(): int
    {
        return $this->memberRepository->countActiveMembers();
    }
}
