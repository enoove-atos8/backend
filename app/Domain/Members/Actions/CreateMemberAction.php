<?php

namespace Domain\Members\Actions;

use Domain\Members\Models\Member;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Throwable;

class CreateMemberAction
{
    private MemberRepository $memberRepository;

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
