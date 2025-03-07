<?php

namespace App\Domain\Members\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;

class GetMemberLeaderAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param int $groupId
     * @param bool $groupLeader
     * @return Collection|Member|Model
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute(int $groupId, bool $groupLeader = true): Member|Model|Collection
    {
        $member = $this->memberRepository->getMemberAsGroupLeader($groupId);

        if($member->count() > 0)
        {
            return $member;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_MEMBER_FOUNDED, 404);
        }
    }
}
