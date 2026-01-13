<?php

namespace App\Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Throwable;

class GetDependentMembersFromListAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * Retorna um array com os IDs dos membros que são dependentes
     * [memberId => principalMemberId, ...]
     *
     * @throws Throwable
     */
    public function execute(array $memberIds): array
    {
        return $this->memberRepository->getDependentMembersFromList($memberIds);
    }
}
