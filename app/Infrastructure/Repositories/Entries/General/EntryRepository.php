<?php

namespace Infrastructure\Repositories\Entries\General;

use Domain\Entries\General\DataTransferObjects\EntryData;
use Domain\Entries\General\Interfaces\EntryRepositoryInterface;
use Domain\Entries\General\Models\Entry;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class EntryRepository extends BaseRepository implements EntryRepositoryInterface
{
    protected mixed $model = Entry::class;
    const DATE_ENTRY_REGISTER_COLUMN = 'date_entry_register';
    const DATE_TRANSACTIONS_COMPENSATION_COLUMN = 'date_transaction_compensation';
    const DELETED_COLUMN = 'deleted';
    const COMPENSATED_COLUMN = 'transaction_compensation';
    const COMPENSATED_VALUE = 'compensated';
    const TO_COMPENSATE_VALUE = 'to_compensate';
    const ID_COLUMN = 'id';
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const AMOUNT_COLUMN = 'amount';
    const DEVOLUTION_COLUMN = 'devolution';
    const TITHE_VALUE = 'tithe';
    const DESIGNATED_VALUE = 'designated';
    const OFFERS_VALUE = 'offers';

    /**
     * Array of where, between and another clauses that was mounted dynamically
     */
    private array $queryClausesAndConditions = [
        'where_clause'    =>  [
            'exists' => false,
            'clause'   =>  [],
        ]
    ];



    /**
     * @param EntryData $entryData
     * @return Entry
     * @throws Throwable
     */
    public function newEntry(EntryData $entryData): Entry
    {
        return $this->create([
            'entry_type'                     =>   $entryData->entryType,
            'transaction_type'               =>   $entryData->transactionType,
            'transaction_compensation'       =>   $entryData->transactionCompensation,
            'date_transaction_compensation'  =>   $entryData->dateTransactionCompensation,
            'date_entry_register'            =>   $entryData->dateEntryRegister,
            'amount'                         =>   floatval($entryData->amount),
            'recipient'                      =>   $entryData->recipient,
            'member_id'                      =>   $entryData->memberId,
            'reviewer_id'                    =>   $entryData->reviewerId,
            'devolution'                     =>   $entryData->devolution,
            'deleted'                        =>   $entryData->deleted,
            'comments'                       =>   $entryData->comments,
            'receipt_link'                   =>   $entryData->receipt,
        ]);
    }


    /**
     * @param string|null $rangeMonthlyDate
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAllEntries(string|null $rangeMonthlyDate): Collection
    {
        if($rangeMonthlyDate !== 'all')
            $arrRangeMonthlyDate = explode(',', $rangeMonthlyDate);

        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->requiredRelationships = ['member'];


        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => self::DELETED_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => false,]
        ];

        if($rangeMonthlyDate !== 'all')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] = [
                'type' => 'andWithOrInside',
                'condition' => ['field' => self::DATE_ENTRY_REGISTER_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]
            ];
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }


    /**
     * @throws BindingResolutionException
     */
    public function getAllEntriesByDateAndType(string|null $date, string $dateType = 'register' | 'transaction', string $entryType = '*'): Collection
    {
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->requiredRelationships = ['member'];

        $this->queryClausesAndConditions['where_clause']['clause'] = [];
        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => self::DELETED_COLUMN,
                            'operator' => BaseRepository::OPERATORS['EQUALS'],
                            'value' => false,
                            ]
        ];

        if($dateType == 'register')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] = [
                'type' => 'and',
                'condition' => ['field' => self::DATE_ENTRY_REGISTER_COLUMN,
                    'operator' => BaseRepository::OPERATORS['LIKE'],
                    'value' => $date,
                ]
            ];
        }
        elseif($dateType == 'transaction')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] = [
                'type' => 'and',
                'condition' => ['field' => self::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                    'operator' => BaseRepository::OPERATORS['LIKE'],
                    'value' => $date,
                ]
            ];
        }


        if ($entryType != '*')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] = [
                'type' => 'and',
                'condition' => ['field' => self::ENTRY_TYPE_COLUMN,
                                'operator' => BaseRepository::OPERATORS['EQUALS'],
                                'value' => $entryType,
                                ]
            ];
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }


    /**
     * @param int $id
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getEntryById(int $id): Model | null
    {
        $this->requiredRelationships = ['member'];

        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,
            ];

        return $this->getItemWithRelationshipsAndWheres($conditions);
    }



    /**
     * @param int $id
     * @param EntryData $entryData
     * @return bool
     * @throws Throwable
     */
    public function updateEntry(int $id, EntryData $entryData): mixed
    {
        $conditions = ['field' => self::ID_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $id,];

        return $this->update($conditions, [
            'entry_type'                     =>   $entryData->entryType,
            'transaction_type'               =>   $entryData->transactionType,
            'transaction_compensation'       =>   $entryData->transactionCompensation,
            'date_transaction_compensation'  =>   $entryData->dateTransactionCompensation,
            'date_entry_register'            =>   $entryData->dateEntryRegister,
            'amount'                         =>   floatval($entryData->amount),
            'recipient'                      =>   $entryData->recipient,
            'member_id'                      =>   $entryData->memberId,
            'reviewer_id'                    =>   $entryData->reviewerId,
            'devolution'                     =>   $entryData->devolution,
            'deleted'                        =>   $entryData->deleted,
            'comments'                       =>   $entryData->comments,
            'receipt_link'                   =>   $entryData->receipt,
        ]);
    }


    /**
     * @param int $id
     * @return mixed
     * @throws BindingResolutionException
     */
    public function deleteEntry(int $id): bool
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,
        ];

        return $this->update($conditions, [
            'deleted' =>   1,
        ]);
    }



    /**
     * @param string $rangeMonthlyDate
     * @param string $amountType
     * @param string|null $entryType
     * @param string|null $exitType
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAmountByEntryType(
        string $rangeMonthlyDate,
        string $amountType,
        string $entryType = null,
        string $exitType = null): Collection
    {
        $arrRangeMonthlyDate = explode(',', $rangeMonthlyDate);
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->queryClausesAndConditions['where_clause']['clause'][] =
            ['type' => 'and', 'condition' => ['field' => self::DELETED_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => false,]];
        $this->queryClausesAndConditions['where_clause']['clause'][] =
            ['type' => 'and', 'condition' => ['field' => self::ENTRY_TYPE_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $entryType,]];
        $this->queryClausesAndConditions['where_clause']['clause'][] =
            ['type' => 'and', 'condition' => ['field' => self::DEVOLUTION_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => false,]];

        if($rangeMonthlyDate !== 'all')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] =
                ['type' => 'andWithOrInside', 'condition' =>  ['field' => self::DATE_ENTRY_REGISTER_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]];
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }


}
