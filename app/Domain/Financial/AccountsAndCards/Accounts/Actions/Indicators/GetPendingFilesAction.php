<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountIndicatorsRepositoryInterface;
use Illuminate\Support\Collection;

class GetPendingFilesAction
{
    public function __construct(
        private AccountIndicatorsRepositoryInterface $repository
    ) {}

    public function execute(): Collection
    {
        return $this->repository->getPendingFiles();
    }
}
