<?php

namespace Domain\Secretary\Membership\Actions;


use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Domain\Secretary\Membership\Models\Member;
use Throwable;

class CreateMemberAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(
        MemberRepositoryInterface $memberRepositoryInterface,
    )
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(MemberData $memberData): Member
    {
        return $this->memberRepository->createMember($memberData);
    }
}
