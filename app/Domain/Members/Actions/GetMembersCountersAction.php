<?php

namespace Domain\Members\Actions;

use App\Domain\Members\Constants\ReturnMessages;
use Domain\Members\Models\Member;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Throwable;

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
    public function __invoke(string $key): array
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
