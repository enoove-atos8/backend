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
        $data = $this->memberRepository->getMembers([], null, false)['results'];

        $members = $data->where(MemberData::MEMBER_TYPE,
            BaseRepository::OPERATORS['EQUALS'],
            MemberRepository::MEMBER_VALUE)->count();

        $congregates = $data->where(MemberData::MEMBER_TYPE,
            BaseRepository::OPERATORS['EQUALS'],
            MemberRepository::CONGREGATE_VALUE)->count();

        $inactives = $data->where(MemberRepository::ACTIVATED_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            false)->count();

        return [
            'indicators' => [
                'members' => $members,
                'congregates' => $congregates,
                'inactives' => $inactives,
            ],
        ];
    }
}
