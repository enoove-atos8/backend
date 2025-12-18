<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Indicators;

use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountIndicatorsRepositoryInterface;

class GetAccountsIndicatorsAction
{
    public function __construct(
        private AccountIndicatorsRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        $accounts = $this->repository->getAccountsIndicators();

        $totalBalance = $accounts->sum(fn ($account) => $account->currentBalance);

        return [
            'accounts' => $accounts,
            'totalBalance' => $totalBalance,
        ];
    }
}
