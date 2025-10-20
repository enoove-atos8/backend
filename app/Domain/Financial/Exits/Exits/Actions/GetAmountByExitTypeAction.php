<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Throwable;

class GetAmountByExitTypeAction
{
    private ExitRepositoryInterface $exitRepository;
    private GetAccountsAction $getAccountsAction;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface,
        GetAccountsAction $getAccountsAction
    )
    {
        $this->exitRepository = $exitRepositoryInterface;
        $this->getAccountsAction = $getAccountsAction;
    }



    /**
     * @throws Throwable
     */
    public function execute($rangeDates, $exitType = 'all'): null | array
    {
        $exits = $this->exitRepository->getAmountByExitType($rangeDates, $exitType);
        $accounts = $this->getAccountsAction->execute();

        $groupByAccounts = function ($collection) use ($accounts)
        {
            return $collection
                ->groupBy(function ($item) use ($accounts)
                {
                    if($item->account_id != null)
                    {
                        $account = $accounts->firstWhere('id', $item->account_id);
                        return $account ? $account->bankName . ' - ' . $account->accountType : 'Conta desconhecida';
                    }
                })
                ->map(function ($items, $key)
                {
                    if($key != '')
                    {
                        [$bankName, $accountType] = explode(' - ', $key);

                        return [
                            'bankName'    => $bankName,
                            'accountType' => $accountType,
                            'total'       => $items->sum(ExitRepository::AMOUNT_COLUMN),
                        ];
                    }

                })
                ->values();
        };

        $payments = $exits->where(ExitRepository::EXIT_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], ExitRepository::PAYMENTS_VALUE);
        $totalPayment = $payments->sum(ExitRepository::AMOUNT_COLUMN);

        $transfers = $exits->where(ExitRepository::EXIT_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], ExitRepository::TRANSFER_VALUE);
        $totalTransfer = $transfers->sum(ExitRepository::AMOUNT_COLUMN);

        $ministerialTransfers = $exits->where(ExitRepository::EXIT_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], ExitRepository::MINISTERIAL_TRANSFER_VALUE);
        $totalMinisterialTransfers = $ministerialTransfers->sum(ExitRepository::AMOUNT_COLUMN);

        $contributions = $exits->where(ExitRepository::EXIT_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], ExitRepository::CONTRIBUTIONS_VALUE);
        $totalContributions = $contributions->sum(ExitRepository::AMOUNT_COLUMN);


        return [
            'payments'              =>  [
                'total'    => $totalPayment,
                'accounts' => $groupByAccounts($payments),
            ],
            'transfers'             =>  [
                'total'    => $totalTransfer,
                'accounts' => $groupByAccounts($transfers),
            ],
            'ministerialTransfers'  =>  [
                'total'    => $totalMinisterialTransfers,
                'accounts' => $groupByAccounts($ministerialTransfers),
            ],
            'contributions'         =>  [
                'total'    => $totalContributions,
                'accounts' => $groupByAccounts($contributions),
            ],
            'total'                 =>  $totalPayment + $totalTransfer + $totalMinisterialTransfers + $totalContributions,
        ];
    }
}
