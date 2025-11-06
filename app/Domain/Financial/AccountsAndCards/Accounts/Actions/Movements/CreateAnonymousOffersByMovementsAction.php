<?php

namespace Domain\Financial\AccountsAndCards\Accounts\Actions\Movements;

use App\Domain\Financial\Entries\Entries\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\AccountsAndCards\Accounts\Actions\Movements\GetMovementsAction;
use App\Infrastructure\Repositories\Financial\Entries\Entries\EntryRepository;
use Carbon\Carbon;
use Domain\Financial\Reviewers\Actions\GetReviewerAction;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class CreateAnonymousOffersByMovementsAction
{
    private GetMovementsAction $getMovementsAction;
    private GetEntriesAction $getEntriesAction;
    private CreateEntryAction $createEntryAction;
    private GetReviewerAction $getReviewerAction;
    private EntryRepositoryInterface $entryRepository;

    public function __construct(
        GetMovementsAction $getMovementsAction,
        GetEntriesAction $getEntriesAction,
        CreateEntryAction $createEntryAction,
        GetReviewerAction $getReviewerAction,
        EntryRepositoryInterface $entryRepository
    )
    {
        $this->getMovementsAction = $getMovementsAction;
        $this->getEntriesAction = $getEntriesAction;
        $this->createEntryAction = $createEntryAction;
        $this->getReviewerAction = $getReviewerAction;
        $this->entryRepository = $entryRepository;
    }

    /**
     * Creates or updates an anonymous offers entry based on movements and registered entries.
     *
     * @param int $accountId
     * @param string $referenceDate Date in format YYYY-MM
     * @return float|null Returns the amount created/updated, or null if no entry was created
     * @throws Throwable
     */
    public function execute(int $accountId, string $referenceDate): ?float
    {

        $movements = $this->getMovementsAction->execute($accountId, $referenceDate, false);

        $totalEntriesInBankExtract = $movements
            ->where('movementType', 'credit')
            ->sum('amount');

        $entries = $this->getEntriesAction->execute($referenceDate, [], false)
            ->where(EntryRepository::ACCOUNT_ID_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], $accountId);

        $totalEntries = $entries
            ->where(EntryRepository::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false)
            ->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['NOT_EQUALS'], EntryRepository::ANONYMOUS_OFFERS_VALUE)
            ->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['NOT_EQUALS'], EntryRepository::ACCOUNTS_TRANSFER_VALUE)
            ->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);

        $totalTransfersBetweenAccounts = $entries
            ->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::ACCOUNTS_TRANSFER_VALUE)
            ->sum(EntryRepository::AMOUNT_COLUMN_WITH_ENTRIES_ALIAS);


        $anonymousOffersAmount = ($totalEntriesInBankExtract - $totalTransfersBetweenAccounts) - $totalEntries;
        $existingAnonymousOffer = $this->getExistingAnonymousOffer($accountId, $referenceDate);

        if ($anonymousOffersAmount > 0) {
            if ($existingAnonymousOffer) {
                return $this->updateAnonymousOffersEntry($existingAnonymousOffer->entries_id, $anonymousOffersAmount);
            } else {
                return $this->createAnonymousOffersEntry($accountId, $referenceDate, $anonymousOffersAmount);
            }
        }

        return null;
    }

    /**
     * Gets existing anonymous offers entry for account and period.
     *
     * @param int $accountId
     * @param string $referenceDate Date in format YYYY-MM
     * @return object|null
     * @throws Throwable
     */
    private function getExistingAnonymousOffer(int $accountId, string $referenceDate): ?object
    {
        $entries = $this->getEntriesAction->execute($referenceDate, [], false)
            ->where(EntryRepository::ACCOUNT_ID_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], $accountId)
            ->where(EntryRepository::ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE, BaseRepository::OPERATORS['EQUALS'], EntryRepository::ANONYMOUS_OFFERS_VALUE)
            ->where(EntryRepository::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], false);

        return $entries->first();
    }

    /**
     * Updates an existing anonymous offers entry.
     *
     * @param int $entryId
     * @param float $amount
     * @return float
     * @throws Throwable
     */
    private function updateAnonymousOffersEntry(int $entryId, float $amount): float
    {
        $reviewer = $this->getReviewerAction->execute();
        $existingEntry = $this->entryRepository->getEntryById($entryId);

        $entryData = new EntryData([
            'id' => $entryId,
            'amount' => $amount,
            'comments' => 'Ofertas anônimas geradas automaticamente após importação de movimentações',
            'dateEntryRegister' => $existingEntry->date_entry_register,
            'dateTransactionCompensation' => $existingEntry->date_transaction_compensation,
            'deleted' => 0,
            'entryType' => EntryRepository::ANONYMOUS_OFFERS_VALUE,
            'memberId' => null,
            'accountId' => $existingEntry->account_id,
            'receipt' => null,
            'devolution' => 0,
            'residualValue' => 0,
            'identificationPending' => 0,
            'cultId' => null,
            'timestampValueCpf' => null,
            'reviewerId' => $reviewer->id,
            'transactionCompensation' => EntryRepository::COMPENSATED_VALUE,
            'transactionType' => EntryRepository::PIX_TRANSACTION_TYPE,
            'groupReceivedId' => null,
            'groupReturnedId' => null,
            'recipient' => null,
            'duplicityVerified' => false,
        ]);


        $this->entryRepository->updateEntry($entryId, $entryData);

        return $amount;
    }

    /**
     * Creates an anonymous offers entry.
     *
     * @param int $accountId
     * @param string $referenceDate
     * @param float $amount
     * @return float
     * @throws Throwable
     */
    private function createAnonymousOffersEntry(int $accountId, string $referenceDate, float $amount): float
    {
        $reviewer = $this->getReviewerAction->execute();

        [$year, $month] = explode('-', $referenceDate);
        $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');


        $entryData = new EntryData([
            'id' => null,
            'amount' => $amount,
            'comments' => 'Ofertas anônimas geradas automaticamente após importação de movimentações',
            'dateEntryRegister' => $lastDayOfMonth,
            'dateTransactionCompensation' => $lastDayOfMonth . 'T03:00:00.000Z',
            'deleted' => 0,
            'entryType' => EntryRepository::ANONYMOUS_OFFERS_VALUE,
            'memberId' => null,
            'accountId' => $accountId,
            'receipt' => null,
            'devolution' => 0,
            'residualValue' => 0,
            'identificationPending' => 0,
            'cultId' => null,
            'timestampValueCpf' => null,
            'reviewerId' => $reviewer->id,
            'transactionCompensation' => EntryRepository::COMPENSATED_VALUE,
            'transactionType' => EntryRepository::PIX_TRANSACTION_TYPE,
            'groupReceivedId' => null,
            'groupReturnedId' => null,
            'recipient' => null,
            'duplicityVerified' => false,
        ]);

        $this->createEntryAction->execute($entryData, null);

        return $amount;
    }
}
