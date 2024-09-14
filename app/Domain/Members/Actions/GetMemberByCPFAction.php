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

class GetMemberByCPFAction
{
    private MemberRepository $memberRepository;

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
    public function __invoke(string $cpf, bool $searchWithMiddleCpf = false): Model | null
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
