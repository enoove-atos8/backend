<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class GetMemberByCPFAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }

    public function execute(string $cpf, bool $searchWithMiddleCpf = false): ?Model
    {
        $member = $this->memberRepository->getMembersByCpf($cpf, $searchWithMiddleCpf);

        if (! is_null($member)) {
            return $member;
        } else {
            return null;
        }
    }
}
