<?php

namespace Domain\Secretary\Membership\Actions;


use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Throwable;

class GetMembersAction
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepositoryInterface)
    {
        $this->memberRepository = $memberRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function execute(array $filters, string | null $term, bool $paginate): array
    {
        $members = $this->memberRepository->getMembers($filters, $term, $paginate);

        return [
            'results' => $members['results'],
            'countRows' => $members['countRows'],
        ];
    }
}
