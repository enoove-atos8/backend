<?php

namespace Domain\Members\Actions;

use Domain\Members\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Infrastructure\Repositories\Member\MemberRepository;

class GetMembersCountersAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }


    /**
     * @param string $key
     * @return array
     * @throws BindingResolutionException
     */
    public function execute(string $key): array
    {
        $counters = null;
        $members = $this->memberRepository->getMembers();

        if($key == 'member' or $key == 'congregate')
            $counters = $members->where('member_type', '=', $key)->count();

        elseif($key == 'inactive')
            $counters = $members->where('activated', '=', 0)->count();

        return [
            'counter'  => $counters,
        ];
    }
}
