<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

class GetMemberByMiddleCPFAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param string $cpf
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function execute(string $cpf): Model | null
    {
        $member = $this->memberRepository->getMembersByMiddleCpf($cpf);

        if(!is_null($member))
        {
            return $member;
        }
        else
        {
            return null;
        }
    }
}
