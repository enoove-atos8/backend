<?php

namespace App\Domain\Secretary\Membership\Actions;

use App\Domain\Financial\Entries\Entries\Actions\GetHistoryTitheByMemberIdAction;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

class GetMembersByGroupIdAction
{
    private MemberRepositoryInterface $memberRepository;
    private GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction;

    public function __construct(
        MemberRepositoryInterface $memberRepositoryInterface,
        GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction
    )
    {
        $this->memberRepository = $memberRepositoryInterface;
        $this->getHistoryTitheByMemberIdAction = $getHistoryTitheByMemberIdAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(int $groupId): ?Collection
    {
        $members = $this->memberRepository->getMembersByGroupId($groupId);

        if ($members) {
            $members = $members->map(function ($member) {
                $member->titheHistory = $this->getHistoryTitheByMemberIdAction->execute($member->id);
                return $member;
            });
        }

        return $members;
    }
}
