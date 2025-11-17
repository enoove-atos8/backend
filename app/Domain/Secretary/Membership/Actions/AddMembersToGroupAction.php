<?php

namespace App\Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Throwable;

class AddMembersToGroupAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(int $groupId, array $memberIds): bool
    {
        return $this->memberRepository->addMembersToGroup($groupId, $memberIds);
    }
}
