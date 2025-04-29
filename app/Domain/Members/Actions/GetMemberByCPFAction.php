<?php

namespace App\Domain\Members\Actions;

use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\Member\MemberRepository;

class GetMemberByCPFAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param string $cpf
     * @param bool $searchWithMiddleCpf
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function execute(string $cpf, bool $searchWithMiddleCpf = false): Model | null
    {
        $member = $this->memberRepository->getMembersByCpf($cpf, $searchWithMiddleCpf);

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
