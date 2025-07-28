<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetTithersByMonthAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param string $month
     * @param bool $paginate
     * @return Collection|Paginator
     * @throws GeneralExceptions
     */
    public function execute(string $month, bool $paginate = false): Collection | Paginator
    {
        $tithers = $this->memberRepository->getTithersByMonth($month, $paginate);

        if(count($tithers) > 0)
        {
            return $tithers;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_MEMBER_FOUNDED, 404);
        }
    }
}
