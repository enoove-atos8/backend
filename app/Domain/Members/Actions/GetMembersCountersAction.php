<?php

namespace Domain\Members\Actions;

use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Member\MemberRepository;

class GetMembersCountersAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @return array
     */
    public function execute(): array
    {
        $data = $this->memberRepository->getMembers([], null, false);

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
            'indicators'  => [
                'members'   =>  $members,
                'congregates'   =>  $congregates,
                'inactives'   =>  $inactives,
            ],
        ];
    }
}
