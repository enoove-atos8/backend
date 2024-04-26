<?php

namespace App\Infrastructure\Repositories\Financial\Entries\General;

use App\Domain\Financial\Entries\General\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\General\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\General\Models\Entry;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Throwable;

class EntryRepository extends BaseRepository implements EntryRepositoryInterface
{
    protected mixed $model = Entry::class;
    const TABLE_NAME = 'entries';
    const DATE_ENTRY_REGISTER_COLUMN = 'date_entry_register';
    const DATE_ENTRY_REGISTER_COLUMN_JOINED = 'entries.date_entry_register';
    const DATE_TRANSACTIONS_COMPENSATION_COLUMN = 'date_transaction_compensation';
    const DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED = 'entries.date_transaction_compensation';
    const DELETED_COLUMN = 'deleted';
    const DELETED_COLUMN_JOINED = 'entries.deleted';
    const REVIEWER_ID_COLUMN_JOINED = 'entries.reviewer_id';
    const COMPENSATED_COLUMN = 'transaction_compensation';
    const COMPENSATED_VALUE = 'compensated';
    const TO_COMPENSATE_VALUE = 'to_compensate';
    const ID_COLUMN = 'id';
    const MEMBER_ID_COLUMN_JOINED = 'entries.member_id';
    const MEMBER_ID_COLUMN = 'member_id';
    const ID_COLUMN_JOINED = 'entries.id';
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const AMOUNT_COLUMN = 'amount';
    const DEVOLUTION_COLUMN = 'devolution';
    const TITHE_VALUE = 'tithe';
    const DESIGNATED_VALUE = 'designated';
    const OFFERS_VALUE = 'offers';
    const REGISTER_INDICATOR = 'register';
    const TRANSACTION_INDICATOR = 'transaction';

    const DISPLAY_SELECT_COLUMNS = [
        'entries.id as entries_id',
        'entries.member_id as entries_member_id',
        'entries.reviewer_id as entries_reviewer_id',
        'entries.entry_type as entries_entry_type',
        'entries.transaction_type as entries_transaction_type',
        'entries.transaction_compensation as entries_transaction_compensation',
        'entries.date_transaction_compensation as entries_date_transaction_compensation',
        'entries.date_entry_register as entries_date_entry_register',
        'entries.amount as entries_amount',
        'entries.recipient as entries_recipient',
        'entries.devolution as entries_devolution',
        'entries.deleted as entries_deleted',
        'entries.comments as entries_comments',
        'entries.receipt_link as entries_receipt_link',
    ];

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
        $arrRangeMonthlyDate = explode(',', $rangeMonthlyDate);

        $this->queryClausesAndConditions['where_clause']['exists'] = true;
        $this->queryClausesAndConditions['where_clause']['clause'] = [];
        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => self::DELETED_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => false,]
        ];

        if($rangeMonthlyDate !== 'all')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] = [
                'type' => 'andWithOrInside',
                'condition' => ['field' => self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]
            ];
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }


    /**
     * @param string|null $rangeMonthlyDate
     * @param string|null $transactionCompensation
     * @param string $orderBy
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAllEntriesWithMembersAndReviewers(string|null $rangeMonthlyDate, string|null $transactionCompensation = 'to_compensate' | 'compensated' | '*', string $orderBy = 'entries.id'): Collection
    {
        $arrRangeMonthlyDate = [];
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            MemberRepository::DISPLAY_SELECT_COLUMNS,
            FinancialReviewerRepository::DISPLAY_SELECT_COLUMNS
        );

        if($rangeMonthlyDate !== 'all')
            $arrRangeMonthlyDate = explode(',', $rangeMonthlyDate);

        $this->queryClausesAndConditions['where_clause']['exists'] = true;
        $this->queryClausesAndConditions['where_clause']['clause'] = [];
        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => self::DELETED_COLUMN_JOINED, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => false,]
        ];

        if($transactionCompensation == 'compensated')
        {
            if($rangeMonthlyDate !== 'all')
            {
                $this->queryClausesAndConditions['where_clause']['clause'][] = [
                    'type' => 'and',
                    'condition' => ['field' => self::COMPENSATED_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => self::COMPENSATED_VALUE,]
                ];

                $this->queryClausesAndConditions['where_clause']['clause'][] = [
                    'type' => 'andWithOrInside',
                    'condition' => ['field' => self::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]
                ];
            }
            else
            {
                $this->queryClausesAndConditions['where_clause']['clause'][] = [
                    'type' => 'and',
                    'condition' => ['field' => self::COMPENSATED_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => self::COMPENSATED_VALUE,]
                ];
            }
        }
        elseif ($transactionCompensation == 'to_compensate')
        {
            if($rangeMonthlyDate !== 'all')
            {

                $this->queryClausesAndConditions['where_clause']['clause'][] = [
                    'type' => 'andWithOrInside',
                    'condition' => ['field' => self::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]
                ];

                $this->queryClausesAndConditions['where_clause']['clause'][] = [
                    'type' => 'and',
                    'condition' => ['field' => self::COMPENSATED_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => self::TO_COMPENSATE_VALUE,]
                ];
            }
            else
            {
                $this->queryClausesAndConditions['where_clause']['clause'][] = [
                    'type' => 'and',
                    'condition' => ['field' => self::COMPENSATED_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => self::TO_COMPENSATE_VALUE,]
                ];
            }
        }

        return $this->qbGetEntriesWithMembersAndReviewers(
            $this->queryClausesAndConditions,
            $displayColumnsFromRelationship,
            $orderBy
        );
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
        $this->queryClausesAndConditions['where_clause']['clause'] = [];

        $this->queryClausesAndConditions['where_clause']['clause'][] =
            ['type' => 'and', 'condition' => ['field' => self::DELETED_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => false,]];
        $this->queryClausesAndConditions['where_clause']['clause'][] =
            ['type' => 'and', 'condition' => ['field' => self::ENTRY_TYPE_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $entryType,]];

        if($rangeMonthlyDate !== 'all')
        {
            $this->queryClausesAndConditions['where_clause']['clause'][] =
                ['type' => 'andWithOrInside', 'condition' =>  ['field' => self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]];
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }



    /*
    |------------------------------------------------------------------------------------------
    | Query Builder queries
    |------------------------------------------------------------------------------------------
    */

    /**
     * Get entries with members and reviewers joins
     *
     * @param array $queryClausesAndConditions
     * @param array $selectColumns
     * @param string $orderBy
     * @param string $sort
     * @return Collection
     * @throws BindingResolutionException
     */
    public function qbGetEntriesWithMembersAndReviewers(
        array $queryClausesAndConditions,
        array $selectColumns,
        string $orderBy = 'entries.id',
        string $sort = 'desc'): Collection
    {
        $query = function () use (
            $queryClausesAndConditions,
            $selectColumns,
            $orderBy,
            $sort) {
            return DB::table(EntryRepository::TABLE_NAME)
                ->select($selectColumns)
                ->leftJoin(
                    MemberRepository::TABLE_NAME,
                    EntryRepository::MEMBER_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    MemberRepository::ID_COLUMN_JOINED)
                ->leftJoin(
                    FinancialReviewerRepository::TABLE_NAME,
                    EntryRepository::REVIEWER_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    FinancialReviewerRepository::ID_COLUMN_JOINED)
                ->where(function ($q) use($queryClausesAndConditions){
                    if($queryClausesAndConditions['where_clause']['exists'] and
                        count($queryClausesAndConditions['where_clause']['clause']) > 0){
                        foreach ($queryClausesAndConditions['where_clause']['clause'] as $key => $clause) {
                            if($clause['type'] == 'and'){
                                if($clause['condition']['operator'] == 'LIKE')
                                {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], "%{$clause['condition']['value']}%");
                                }

                                else
                                {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                                }
                            }
                            if($clause['type'] == 'andWithOrInside'){
                                $q->where(function($query) use($clause){
                                    if(count($clause['condition']) > 0){
                                        foreach ($clause['condition']['value'] as $value){
                                            $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], "%{$value}%");
                                        }
                                    }
                                });
                            }
                            if($clause['type'] == 'or'){
                                $q->orWhere($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                            }
                            if($clause['type'] == 'in'){
                                $q->whereIn($clause['condition']['field'], $clause['condition']['value']);
                            }
                            if($clause['type'] == 'not_in'){
                                $q->whereNot($clause['condition']['field'], $clause['condition']['value']);
                            }
                        }
                    }
                })
                ->orderBy($orderBy, $sort)
                ->get();
        };

        return $this->doQuery($query);
    }


}
