<?php

namespace Infrastructure\Repositories\Financial\Exits\Exits;

use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Domain\Financial\Exits\Exits\Models\Exits;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Divisions\DivisionRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentCategoryRepository;
use Infrastructure\Repositories\Financial\Exits\Payments\PaymentItemRepository;

class ExitRepository extends BaseRepository implements ExitRepositoryInterface
{

    protected mixed $model = Exits::class;

    const TABLE_NAME = 'exits';

    const DATE_TRANSACTIONS_COMPENSATION_COLUMN = 'date_transaction_compensation';
    const PIX_VALUE = 'pix';
    const EXITS_VALUE = 'exits';
    const TRANSACTIONS_COMPENSATION_COLUMN = 'transaction_compensation';
    const TRANSACTIONS_COMPENSATION_COLUMN_JOINED = 'exits.transaction_compensation';
    const DATE_EXIT_REGISTER_COLUMN = 'date_exit_register';
    const DATE_EXIT_REGISTER_COLUMN_JOINED = 'exits.date_exit_register';
    const DELETED_COLUMN = 'deleted';
    const DELETED_COLUMN_JOINED = 'exits.deleted';
    const COMPENSATED_VALUE = 'compensated';
    const TO_COMPENSATE_VALUE = 'to_compensate';
    const TIMESTAMP_EXIT_TRANSACTION_COLUMN = 'timestamp_exit_transaction';

    const REVIEWER_ID_COLUMN_JOINED = 'exits.reviewer_id';
    const DIVISION_ID_COLUMN_JOINED = 'exits.division_id';
    const GROUP_ID_COLUMN_JOINED = 'exits.group_id';
    const PAYMENT_CATEGORY_ID_COLUMN_JOINED = 'exits.payment_category_id';
    const PAYMENT_ITEM_ID_COLUMN_JOINED = 'exits.payment_item_id';

    const ID_COLUMN_JOINED = 'exits.id';
    const AMOUNT_COLUMN = 'amount';
    const AMOUNT_COLUMN_JOINED = 'exits.amount';
    const AMOUNT_COLUMN_JOINED_WITH_UNDERLINE = 'exits_amount';
    const EXIT_TYPE_COLUMN = 'exit_type';
    const PAYMENT_VALUE = 'payment';
    const TRANSFER_VALUE = 'transfer';
    const MINISTERIAL_TRANSFER_VALUE = 'ministerial_transfer';
    const CONTRIBUTIONS_VALUE = 'contributions';

    const PAGINATE_NUMBER = 30;

    const DISPLAY_SELECT_COLUMNS = [
        'exits.id as exits_id',
        'exits.reviewer_id as exits_reviewer_id',
        'exits.exit_type as exits_exit_type',
        'exits.division_id as exits_division_id',
        'exits.group_id as exits_group_id',
        'exits.payment_category_id as exits_payment_category_id',
        'exits.payment_item_id as exits_payment_item_id',
        'exits.is_payment as exits_is_payment',
        'exits.deleted as exits_deleted',
        'exits.transaction_type as exits_transaction_type',
        'exits.transaction_compensation as exits_transaction_compensation',
        'exits.date_transaction_compensation as exits_date_transaction_compensation',
        'exits.date_exit_register as exits_date_exit_register',
        'exits.timestamp_exit_transaction as exits_timestamp_exit_transaction',
        'exits.amount as exits_amount',
        'exits.comments as exits_comments',
        'exits.receipt_link as exits_receipt_link',
    ];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param ExitData $exitData
     * @return Exits
     */
    public function newExit(ExitData $exitData): Exits
    {
        return $this->create([
            'reviewer_id'                        =>   $exitData->financialReviewer->id,
            'exit_type'                          =>   $exitData->exitType,
            'division_id'                        =>   $exitData->division->id,
            'group_id'                           =>   $exitData->group->id,
            'payment_category_id'                =>   $exitData->paymentCategory->id,
            'payment_item_id'                    =>   $exitData->paymentItem->id,
            'is_payment'                         =>   $exitData->isPayment,
            'deleted'                            =>   $exitData->deleted,
            'transaction_type'                   =>   $exitData->transactionType,
            'transaction_compensation'           =>   $exitData->transactionCompensation,
            'date_transaction_compensation'      =>   $exitData->dateTransactionCompensation,
            'date_exit_register'                 =>   $exitData->dateExitRegister,
            'timestamp_exit_transaction'         =>   $exitData->timestampExitTransaction,
            'amount'                             =>   floatval($exitData->amount),
            'comments'                           =>   $exitData->comments,
            'receipt_link'                       =>   $exitData->receiptLink,
        ]);
    }


    /**
     * @param string|null $dates
     * @param array $filters
     * @param string $transactionCompensation
     * @param bool $paginate
     * @param bool $queryOnlyExitsTable
     * @return Collection|Paginator
     * @throws BindingResolutionException
     */
    public function getExits(?string $dates, array $filters, string $transactionCompensation = self::COMPENSATED_VALUE, bool $paginate = true, bool $queryOnlyExitsTable = false): Collection | Paginator
    {
        $this->queryConditions = [];
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            FinancialReviewerRepository::DISPLAY_SELECT_COLUMNS,
            DivisionRepository::DISPLAY_SELECT_COLUMNS,
            GroupsRepository::DISPLAY_SELECT_COLUMNS,
            PaymentCategoryRepository::DISPLAY_SELECT_COLUMNS,
            PaymentItemRepository::DISPLAY_SELECT_COLUMNS
        );

        if($dates != 'all' && $dates != null)
            $arrDates = explode(',', $dates);


        $this->queryConditions [] = $this->whereEqual(self::DELETED_COLUMN_JOINED, false, 'and');

        if($transactionCompensation == self::COMPENSATED_VALUE)
        {
            $this->queryConditions [] = $this->whereEqual(self::TRANSACTIONS_COMPENSATION_COLUMN_JOINED, $transactionCompensation, 'and');

            if($dates !== 'all' && $dates != null)
                $this->queryConditions [] = $this->whereLike(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, $arrDates, 'andWithOrInside');

            //if(count($filters) > 0)
            //    $this->applyFilters($filters,true);

        }
        elseif ($transactionCompensation == self::TO_COMPENSATE_VALUE)
        {
            if($dates !== 'all')
            {
                $this->queryConditions [] = $this->whereLike(self::DATE_EXIT_REGISTER_COLUMN_JOINED, $arrDates, 'andWithOrInside');
                $this->queryConditions [] = $this->whereEqual(self::TRANSACTIONS_COMPENSATION_COLUMN_JOINED, $transactionCompensation, 'andWithOrInside');
            }
            else
            {
                $this->queryConditions [] = $this->whereEqual(self::TRANSACTIONS_COMPENSATION_COLUMN, $transactionCompensation, 'and');
            }
        }

        if($queryOnlyExitsTable)
            return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
        else
            return $this->qbGetExitsWithReviewers($this->queryConditions, $displayColumnsFromRelationship, (array)self::ID_COLUMN_JOINED, $paginate);
    }


    /**
     * @param string $dates
     * @param string $exitType
     * @return mixed
     * @throws BindingResolutionException
     */
    public function getAmountByExitType(string $dates, string $exitType = '*'): mixed
    {
        $this->queryConditions = [];
        $arrDates = explode(',', $dates);

        $this->queryConditions [] = $this->whereEqual(self::DELETED_COLUMN, false, 'and');

        if($exitType != 'all')
            $this->queryConditions [] = $this->whereEqual(self::EXIT_TYPE_COLUMN, $exitType, 'and');

        if($dates !== 'all')
            $this->queryConditions [] = $this->whereLike(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, $arrDates, 'andWithOrInside');

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }


    /*public function applyFilters(array $filters, bool $returnConditions = false)
    {
        $this->queryConditions = count($this->queryConditions) > 0 ? $this->queryConditions : [];

        foreach ($filters as $key => $filter)
        {

        }

        if($returnConditions)
            return $this->queryConditions;
    }*/


    /**
     * @param int $exitId
     * @param string $timestamp
     * @return mixed
     * @throws BindingResolutionException
     */
    public function updateTimestamp(int $exitId, string $timestamp): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $exitId,
            ];

        return $this->update($conditions, [
            'timestamp_exit_transaction'  =>   $timestamp,
        ]);
    }


    /**
     * @param int $exitId
     * @param string $link
     * @return mixed
     * @throws BindingResolutionException
     */
    public function updateReceiptLink(int $exitId, string $link): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $exitId,
            ];

        return $this->update($conditions, [
            'receipt_link'  =>   $link,
        ]);
    }


    /**
     * @param string $timestamp
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getExitByTimestamp(string $timestamp): Model | null
    {
        $this->queryConditions = [];

        $this->queryConditions [] = $this->whereEqual(self::TIMESTAMP_EXIT_TRANSACTION_COLUMN, $timestamp, 'and');

        return $this->getItemWithRelationshipsAndWheres($this->queryConditions);
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
     * @param array $orderBy
     * @param bool $paginate
     * @param string $sort
     * @return Collection | Paginator
     * @throws BindingResolutionException
     */
    public function qbGetExitsWithReviewers(
        array $queryClausesAndConditions,
        array $selectColumns,
        array $orderBy,
        bool $paginate = true,
        string $sort = 'desc'): Collection | Paginator
    {
        $query = function () use (
            $queryClausesAndConditions,
            $selectColumns,
            $orderBy,
            $sort,
            $paginate) {

            $q = DB::table(ExitRepository::TABLE_NAME)
                ->select($selectColumns)
                ->leftJoin(
                    FinancialReviewerRepository::TABLE_NAME,
                    ExitRepository::REVIEWER_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    FinancialReviewerRepository::ID_COLUMN_JOINED)
                ->leftJoin(
                    DivisionRepository::TABLE_NAME,
                    ExitRepository::DIVISION_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    DivisionRepository::ID_COLUMN_JOINED)
                ->leftJoin(
                    GroupsRepository::TABLE_NAME,
                    ExitRepository::GROUP_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    GroupsRepository::ID_COLUMN_JOINED)
                ->leftJoin(
                    PaymentCategoryRepository::TABLE_NAME,
                    ExitRepository::PAYMENT_CATEGORY_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    PaymentCategoryRepository::ID_COLUMN_JOINED)
                ->leftJoin(
                    PaymentItemRepository::TABLE_NAME,
                    ExitRepository::PAYMENT_ITEM_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    PaymentItemRepository::ID_COLUMN_JOINED)
                ->where(function ($q) use($queryClausesAndConditions){
                    if(count($queryClausesAndConditions) > 0){
                        foreach ($queryClausesAndConditions as $key => $clause) {
                            if($clause['type'] == 'and')
                            {
                                if($clause['condition']['operator'] == BaseRepository::OPERATORS['LIKE'])
                                {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], "%{$clause['condition']['value']}%");
                                }
                                if($clause['condition']['operator'] == BaseRepository::OPERATORS['EQUALS'])
                                {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                                }
                                if($clause['condition']['operator'] == BaseRepository::OPERATORS['IS_NULL'])
                                {
                                    $q->whereNull($clause['condition']['field']);
                                }
                                if($clause['condition']['operator'] == BaseRepository::OPERATORS['BETWEEN'])
                                {
                                    $arrDates = explode(',', $clause['condition']['value'][0]);
                                    $q->whereBetween($clause['condition']['field'], $arrDates);
                                }
                            }
                            if($clause['type'] == 'andWithOrInside')
                            {
                                $q->where(function($query) use($clause)
                                {
                                    if(count($clause['condition']) > 0)
                                    {
                                        if($clause['condition']['operator'] == BaseRepository::OPERATORS['EQUALS'])
                                        {
                                            foreach ($clause['condition']['value'] as $value)
                                            {
                                                $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], $value);
                                            }
                                        }
                                        if($clause['condition']['operator'] == BaseRepository::OPERATORS['LIKE'])
                                        {
                                            foreach ($clause['condition']['value'] as $value){
                                                $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], "%{$value}%");
                                            }
                                        }
                                        if($clause['condition']['operator'] == BaseRepository::OPERATORS['IS_NULL'])
                                        {
                                            $query->orWhereNull($clause['condition']['field']);
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
                });

            if(count($orderBy) > 0)
                foreach ($orderBy as $clause)
                    $q->orderByDesc($clause);

            if($paginate)
            {
                $paginator = $q->simplePaginate(self::PAGINATE_NUMBER);
                return $paginator->setCollection($paginator->getCollection()->map(fn($item) => ExitData::fromArray((array) $item)));
            }
            else
            {
                return collect($q)->map(fn($item) => ExitData::fromArray((array) $item));
            }
        };

        return $this->doQuery($query);
    }
}
