<?php

namespace Domain\Members\Actions;

use App\Domain\Members\Constants\ReturnMessages;
use Domain\Members\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Throwable;

class GetMembersAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function __invoke(): Member|Model|Collection
    {
        $members = $this->memberRepository->getMembers();

        if($members->count() > 0)
        {
            return $members;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_MEMBERS_FOUNDED, 404);
        }
    }
}
