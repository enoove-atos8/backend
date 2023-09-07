<?php

namespace Infrastructure\Repositories\Entries;

use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\Models\Entry;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;

class EntryRepository extends BaseRepository implements EntryRepositoryInterface
{
    protected mixed $model = Entry::class;

    /**
     * @throws \Throwable
     */
    public function newEntry(EntryData $entryData): Entry
    {
        $amountFormattedWithoutDot = str_replace('.', '', $entryData->amount);
        $amountFormattedWithoutComma = str_replace(',', '.', $amountFormattedWithoutDot);
        $entry = $this->create([
            'entry_type'                     =>   $entryData->entryType,
            'transaction_type'               =>   $entryData->transactionType,
            'transaction_compensation'       =>   $entryData->transactionCompensation,
            'date_transaction_compensation'  =>   $entryData->dateTransactionCompensation,
            'date_entry_register'            =>   $entryData->dateEntryRegister,
            'amount'                         =>   floatval($amountFormattedWithoutComma),
            'recipient'                      =>   $entryData->recipient,
            'member_id'                      =>   $entryData->memberId,
            'reviewer_id'                    =>   $entryData->reviewerId,
        ]);



        throw_if(!$entry, GeneralExceptions::class, 'Houve um erro ao procesar o cadastro de uma nova igreja', 500);

        return $entry;
    }

    /**
     * @throws BindingResolutionException
     */
    public function getAllEntries(string $startDate, string $endDate): Collection
    {
        $this->requiredRelationships = ['member'];

        $whereConditions = [];
        $betweenConditions = [$startDate, $endDate];
        $betweenColumn = 'date_entry_register';

        $entries = $this->getItemsWithRelationshipsAndWhere($whereConditions,$betweenColumn, $betweenConditions);

        return $entries;
    }
}
