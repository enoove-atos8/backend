<?php

namespace Domain\Members\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Members\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Throwable;

class GetMemberByIdAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param null $id
     * @return Model
     * @throws GeneralExceptions
     */
    public function __invoke($id = null): Model
    {
        $member = $this->memberRepository->getMembers($id);

        if(!is_object($member))
            throw new GeneralExceptions('Erro ao retornar este usuário', 500);

        if($member->count() == 0)
            throw new GeneralExceptions('Nenhum usuário encontrado!', 404);

        return $member;
    }
}
