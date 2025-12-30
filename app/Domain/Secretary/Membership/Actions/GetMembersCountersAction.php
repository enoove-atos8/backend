<?php

namespace Domain\Secretary\Membership\Actions;

use App\Infrastructure\Repositories\Secretary\Membership\MemberRepository;
use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;

class GetMembersCountersAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    public function execute(): array
    {
        // Busca membros ativos
        $activeData = $this->memberRepository->getMembers([], null, false)['results'];

        $members = $activeData->where(MemberData::MEMBER_TYPE,
            BaseRepository::OPERATORS['EQUALS'],
            MemberRepository::MEMBER_VALUE)->count();

        $congregates = $activeData->where(MemberData::MEMBER_TYPE,
            BaseRepository::OPERATORS['EQUALS'],
            MemberRepository::CONGREGATE_VALUE)->count();

        // Busca membros inativos separadamente
        $inactiveData = $this->memberRepository->getMembers(['memberTypes' => 'inactive'], null, false)['results'];
        $inactives = $inactiveData->count();

        return [
            'indicators' => [
                'members' => $members,
                'congregates' => $congregates,
                'inactives' => $inactives,
            ],
        ];
    }
}
