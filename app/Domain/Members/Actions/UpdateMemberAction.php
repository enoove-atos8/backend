<?php

namespace Domain\Members\Actions;

use Domain\Users\DataTransferObjects\UserDetailData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Member\MemberRepository;
use Domain\Members\DataTransferObjects\MemberData;
use Domain\Members\Interfaces\MemberRepositoryInterface;
use Domain\Members\Models\Member;
use Throwable;

class UpdateMemberAction
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    /**
     * @param $id
     * @param MemberData $memberData
     * @return Member
     */
    public function __invoke($id, MemberData $memberData): Member
    {
        return $this->memberRepository->updateMember($id, $memberData);
    }
}
