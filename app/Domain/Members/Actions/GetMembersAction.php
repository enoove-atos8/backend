<?php

namespace Domain\Members\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Throwable;

class GetMembersAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function execute(array $filters, string | null $term, bool $paginate): Collection | Paginator
    {
        return $this->memberRepository->getMembers(
            $filters,
            $term,
            $paginate
        );
    }
}
