<?php

namespace Infrastructure\Repositories\Entries;

use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\Models\Entry;
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
        $entry = $this->create([
            'entryType'                     =>   $entryData->entryType,
            'transactionType'               =>   $entryData->transactionType,
            'transactionCompensation'       =>   $entryData->transactionCompensation,
            'dateTransactionCompensation'   =>   $entryData->dateTransactionCompensation,
            'dateEntryRegister'             =>   $entryData->dateEntryRegister,
            'amount'                        =>   $entryData->amount,
            'recipient'                     =>   $entryData->recipient,
            'memberId'                      =>   $entryData->memberId,
            'reviewerId'                    =>   $entryData->reviewerId,
        ]);



        throw_if(!$entry, GeneralExceptions::class, 'Houve um erro ao procesar o cadastro de uma nova igreja', 500);

        return $entry;
    }
}
