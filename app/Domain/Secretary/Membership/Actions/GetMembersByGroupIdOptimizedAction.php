<?php

namespace App\Domain\Secretary\Membership\Actions;

use App\Domain\Financial\Entries\Entries\Actions\GetHistoryTitheByMemberIdsAction;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

class GetMembersByGroupIdOptimizedAction
{
    private MemberRepositoryInterface $memberRepository;
    private GetHistoryTitheByMemberIdsAction $getHistoryTitheByMemberIdsAction;

    public function __construct(
        MemberRepositoryInterface $memberRepositoryInterface,
        GetHistoryTitheByMemberIdsAction $getHistoryTitheByMemberIdsAction
    )
    {
        $this->memberRepository = $memberRepositoryInterface;
        $this->getHistoryTitheByMemberIdsAction = $getHistoryTitheByMemberIdsAction;
    }

    /**
     * Busca membros do grupo com histórico de dízimos otimizado
     * Reduz de N+1 queries para apenas 3 queries:
     * 1. Buscar membros do grupo
     * 2. Buscar dependentes (1 query)
     * 3. Buscar histórico de dízimos de todos (1 query)
     *
     * @throws Throwable
     */
    public function execute(int $groupId): ?Collection
    {
        $members = $this->memberRepository->getMembersByGroupId($groupId);

        if ($members && $members->count() > 0) {
            // Extrai IDs de todos os membros
            $memberIds = $members->pluck('id')->toArray();

            // Busca histórico de todos os membros de uma vez (otimizado)
            $titheHistories = $this->getHistoryTitheByMemberIdsAction->execute($memberIds);

            // Adiciona o histórico a cada membro
            $members = $members->map(function ($member) use ($titheHistories) {
                $member->titheHistory = $titheHistories[$member->id] ?? [
                    'isDependent' => false,
                    'history' => [],
                ];
                return $member;
            });
        }

        return $members;
    }
}
