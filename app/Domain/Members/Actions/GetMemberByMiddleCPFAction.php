<?php

namespace App\Domain\Members\Actions;

use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\Member\MemberRepository;

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
