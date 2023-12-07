<?php

namespace Domain\Members\Actions;

use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
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
    public function __invoke(MemberData $memberData): Member
    {
        return $this->memberRepository->createMember($memberData);
    }
}
