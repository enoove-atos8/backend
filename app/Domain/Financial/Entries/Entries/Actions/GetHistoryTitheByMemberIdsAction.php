<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Secretary\Membership\Actions\GetDependentMembersFromListAction;
use Throwable;

class GetHistoryTitheByMemberIdsAction
{
    public function __construct(
        private GetTithesByMemberIdsAction $getTithesByMemberIdsAction,
        private GetDependentMembersFromListAction $getDependentMembersFromListAction
    ) {
    }

    /**
     * Busca o histórico de dízimos para múltiplos membros de uma só vez (otimizado)
     * Usa apenas 3 queries:
     * 1. Buscar dependentes (1 query)
     * 2. Buscar todos os dízimos (1 query)
     * 3. Processar e montar o resultado (em memória)
     *
     * @throws Throwable
     */
    public function execute(array $memberIds, int $months = 6): array
    {
        if (empty($memberIds)) {
            return [];
        }

        // 1. Busca quais membros são dependentes (1 query)
        $dependents = $this->getDependentMembersFromListAction->execute($memberIds);

        // 2. Gera estrutura dos últimos N meses
        $history = [];
        $currentDate = now();
        for ($i = 1; $i <= $months; $i++) {
            $monthKey = $currentDate->copy()->subMonths($i)->format('Y-m');
            $history[$monthKey] = false;
        }

        // 3. Busca TODOS os dízimos dos membros de uma vez (1 query)
        $tithes = $this->getTithesByMemberIdsAction->execute($memberIds);

        // 4. Organiza os dízimos por membro (processamento em memória)
        $tithesByMember = [];
        foreach ($tithes as $tithe) {
            $memberId = $tithe->member_id;
            $date = $tithe->date_transaction_compensation;

            if (!isset($tithesByMember[$memberId])) {
                $tithesByMember[$memberId] = [];
            }

            if (!empty($date)) {
                $monthKey = substr($date, 0, 7); // YYYY-MM
                $tithesByMember[$memberId][$monthKey] = true;
            }
        }

        // 5. Monta o resultado final para cada membro (processamento em memória)
        $result = [];
        foreach ($memberIds as $memberId) {
            $memberHistory = $history;

            // Marca os meses que o membro devolveu
            if (isset($tithesByMember[$memberId])) {
                foreach ($tithesByMember[$memberId] as $monthKey => $value) {
                    if (array_key_exists($monthKey, $memberHistory)) {
                        $memberHistory[$monthKey] = true;
                    }
                }
            }

            $result[$memberId] = [
                'isDependent' => isset($dependents[$memberId]),
                'history' => $memberHistory,
            ];
        }

        return $result;
    }
}
