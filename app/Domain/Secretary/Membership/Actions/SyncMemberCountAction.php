<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\CentralDomain\Churches\Church\Actions\UpdateChurchAction;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;

class SyncMemberCountAction
{
    public function __construct(
        private CountActiveMembersAction $countActiveMembersAction,
        private ChurchRepositoryInterface $churchRepository,
        private UpdateChurchAction $updateChurchAction
    ) {}

    /**
     * Sincroniza o member_count na tabela churches com a contagem real de membros ativos.
     */
    public function execute(): bool
    {
        $tenantId = tenant('id');

        if (! $tenantId) {
            return false;
        }

        $church = $this->churchRepository->getChurch($tenantId);

        if (! $church) {
            return false;
        }

        $memberCount = $this->countActiveMembersAction->execute();

        return $this->updateChurchAction->execute($church->id, [
            'member_count' => $memberCount,
        ]);
    }
}
