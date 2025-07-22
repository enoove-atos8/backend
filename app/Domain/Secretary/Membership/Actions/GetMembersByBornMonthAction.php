<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetMembersByBornMonthAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param string $month
     * @param string $fields
     * @return Collection
     * @throws GeneralExceptions
     */
    public function execute(string $month, string $fields = null): Collection
    {
        $members = $this->memberRepository->getMembersByBornMonth($month, $fields);

        if(count($members) > 0)
        {
            return $members;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_MEMBER_FOUNDED, 404);
        }
    }
}
