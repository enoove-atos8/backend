<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use Throwable;

class GetHistoryTitheByMemberIdAction
{
    private EntryRepositoryInterface $entryRepository;

    public function __construct(EntryRepositoryInterface $entryRepositoryInterface)
    {
        $this->entryRepository = $entryRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(int $memberId, int $months = 6): array
    {
        return $this->entryRepository->getHistoryTitheByMemberId($memberId, $months);
    }
}
