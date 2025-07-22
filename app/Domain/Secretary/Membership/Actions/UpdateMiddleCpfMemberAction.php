<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateMiddleCpfMemberAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param int $memberId
     * @param string $middleCpf
     * @return true
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
