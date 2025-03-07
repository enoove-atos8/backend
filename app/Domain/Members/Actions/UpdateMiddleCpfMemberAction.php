<?php

namespace App\Domain\Members\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;

class UpdateMiddleCpfMemberAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param int $memberId
     * @param string $middleCpf
     * @return true
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function execute(int $memberId, string $middleCpf): mixed
    {
        $updatedMember = $this->memberRepository->updateMiddleCpf($memberId, $middleCpf);

        if($updatedMember)
        {
            return true;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_STATUS_MEMBER, 500);
        }
    }
}
