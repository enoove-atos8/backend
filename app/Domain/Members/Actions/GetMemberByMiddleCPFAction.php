<?php

namespace App\Domain\Members\Actions;

use App\Domain\Members\Constants\ReturnMessages;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Throwable;

class GetMemberByMiddleCPFAction
{
    private MemberRepository $memberRepository;

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
