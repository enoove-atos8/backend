<?php

namespace Domain\Members\Actions;

use App\Domain\Members\Constants\ReturnMessages;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Throwable;

class GetMemberByIdAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param null $id
     * @return Collection|Member|Model
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function execute($id = null): Member|Model|Collection
    {
        $member = $this->memberRepository->getMembers($id);

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
