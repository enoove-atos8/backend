<?php

namespace App\Domain\Financial\Entries\Entries\Actions;

use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Domain\Secretary\Membership\Actions\GetMembersAction;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class GetAmountByEntryTypeAction
{
    private EntryRepositoryInterface $entryRepository;
    private GetMembersAction $getMembersAction;
    private GetAccountsAction $getAccountsAction;

    public function __construct(
        EntryRepositoryInterface $entryRepositoryInterface,
        GetMembersAction $getMembersAction,
        GetAccountsAction $getAccountsAction
    )
    {
        $this->entryRepository = $entryRepositoryInterface;
        $this->getMembersAction = $getMembersAction;
        $this->getAccountsAction = $getAccountsAction;
    }

    /**
     * @throws Throwable
     */
    public function execute($rangeMonthlyDate, $entryType = 'all'): ?array
    {
        $entries = $this->entryRepository->getAmountByEntryType($rangeMonthlyDate, $entryType);
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
                            'total'        => $items->sum(EntryRepository::AMOUNT_COLUMN),
                        ];
                    }

                })
                ->values();
        };

        // === DÍZIMOS ===
        $tithes = $entries->where(
            EntryRepository::ENTRY_TYPE_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            EntryRepository::TITHE_VALUE
        );
        $totalTithesAmount = $tithes->sum(EntryRepository::AMOUNT_COLUMN);

        // === OFERTAS ===
        $offers = $entries->where(
            EntryRepository::ENTRY_TYPE_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            EntryRepository::OFFER_VALUE
        );
        $totalOfferAmount = $offers->sum(EntryRepository::AMOUNT_COLUMN);

        // === DESIGNADAS ===
        $designated = $entries->where(
            EntryRepository::ENTRY_TYPE_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            EntryRepository::DESIGNATED_VALUE
        );
        $totalDesignatedAmount = $designated->sum(EntryRepository::AMOUNT_COLUMN);

        // === DEVOLUÇÕES (não faz sentido por conta, apenas total) ===
        $totalDevolutionAmount = $entries->where(
            EntryRepository::DEVOLUTION_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            1
        )->sum(EntryRepository::AMOUNT_COLUMN);

        return [
            'tithes'     => [
                'total'    => $totalTithesAmount,
                'accounts' => $groupByAccounts($tithes),
            ],
            'offers'      => [
                'total'    => $totalOfferAmount,
                'accounts' => $groupByAccounts($offers),
            ],
            'designated' => [
                'total'    => $totalDesignatedAmount,
                'accounts' => $groupByAccounts($designated) ?? null,
            ],
            'devolution' => $totalDevolutionAmount,
        ];
    }
}
