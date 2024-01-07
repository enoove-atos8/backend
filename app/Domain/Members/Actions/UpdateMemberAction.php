<?php

namespace Domain\Members\Actions;

use App\Domain\Members\Constants\ReturnMessages;
use Domain\Users\DataTransferObjects\UserDetailData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Throwable;

class UpdateMemberAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param $id
     * @param MemberData $memberData
     * @return true
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function __invoke($id, MemberData $memberData): bool
    {
        if($this->memberRepository->updateMember($id, $memberData))
        {
            return true;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_MEMBER, 500);
        }
    }
}
