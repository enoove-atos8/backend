<?php

namespace Domain\Members\Actions;

use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\User\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
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
    public function __invoke(): Collection
    {
        $member = $this->memberRepository->getUsers();

        if($member->count() == 0)
            throw new GeneralExceptions('Nenhum membro encontrado!', 404);

        return $member;
    }
}
