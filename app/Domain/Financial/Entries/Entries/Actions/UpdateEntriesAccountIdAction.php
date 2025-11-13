<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;

class UpdateEntriesAccountIdAction
{
    private EntryRepositoryInterface $entryRepository;

    public function __construct(EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * Atualiza o account_id de múltiplas entradas (segunda camada de identificação de contas)
     *
     * @param array $entryIds
     * @param int $accountId
     * @return bool
     */
    public function execute(array $entryIds, int $accountId): bool
    {
        if (empty($entryIds)) {
            return true;
        }

        return $this->entryRepository->bulkUpdateAccountId($entryIds, $accountId);
    }
}
