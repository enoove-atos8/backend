<?php

namespace Domain\Members\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;

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
    public function execute($id, MemberData $memberData): bool
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
