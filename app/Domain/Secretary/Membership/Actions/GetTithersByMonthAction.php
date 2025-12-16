<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\Financial\Entries\Entries\Actions\GetHistoryTitheByMemberIdAction;
use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetTithersByMonthAction
{
    private MemberRepositoryInterface $memberRepository;

    private GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction;

    public function __construct(
        MemberRepositoryInterface $memberRepositoryInterface,
        GetHistoryTitheByMemberIdAction $getHistoryTitheByMemberIdAction
    ) {
        $this->memberRepository = $memberRepositoryInterface;
        $this->getHistoryTitheByMemberIdAction = $getHistoryTitheByMemberIdAction;
    }

    /**
     * @throws GeneralExceptions
     */
    public function execute(string $month, bool $paginate = false): Collection|Paginator
    {
        $tithers = $this->memberRepository->getTithersByMonth($month, $paginate);

        if (count($tithers) > 0) {
            if ($paginate) {
                $tithers->setCollection(
                    $tithers->getCollection()->map(function ($member) {
                        $member->titheHistory = $this->getHistoryTitheByMemberIdAction->execute($member->id);

                        return $member;
                    })
                );
            } else {
                $tithers = $tithers->map(function ($member) {
                    $member->titheHistory = $this->getHistoryTitheByMemberIdAction->execute($member->id);

                    return $member;
                });
            }

            return $tithers;
        } else {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_MEMBER_FOUNDED, 404);
        }
    }
}
