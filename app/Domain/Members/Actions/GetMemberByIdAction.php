<?php

namespace Domain\Members\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;

class GetMemberByIdAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param null $id
     * @return MemberData
     * @throws GeneralExceptions
     */
    public function execute($id = null): MemberData
    {
        $member = $this->memberRepository->getMemberById($id);

        if(!is_null($member))
        {
            return $member;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::INFO_NO_MEMBER_FOUNDED, 404);
        }
    }
}
