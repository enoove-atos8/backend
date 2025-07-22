<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Domain\Secretary\Membership\Models\Member;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetMemberLeaderAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param int $groupId
     * @param bool $groupLeader
     * @return Collection|Member|Model
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
