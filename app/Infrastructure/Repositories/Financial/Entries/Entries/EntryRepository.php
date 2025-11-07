<?php

namespace App\Infrastructure\Repositories\Financial\Entries\Entries;

use App\Domain\Financial\Entries\DuplicitiesAnalisys\DataTransferObjects\DuplicityAnalisysEntriesData;
use App\Domain\Financial\Entries\Entries\DataTransferObjects\EntryData;
use App\Domain\Financial\Entries\Entries\Interfaces\EntryRepositoryInterface;
use App\Domain\Financial\Entries\Entries\Models\Entry;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Domain\Financial\Entries\DuplicitiesAnalisys\DataTransferObjects\ReceiptsByIdsData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Accounts\AccountRepository;
use Infrastructure\Repositories\Member\MemberRepository;
use Throwable;

class EntryRepository extends BaseRepository implements EntryRepositoryInterface
{
    protected mixed $model = Entry::class;

    const TABLE_NAME = 'entries';

    const ENTRY_TYPE = 'entry';

    const DATE_ENTRY_REGISTER_COLUMN = 'date_entry_register';

    const DATE_ENTRY_REGISTER_COLUMN_JOINED = 'entries.date_entry_register';

    const DATE_TRANSACTIONS_COMPENSATION_COLUMN = 'date_transaction_compensation';

    const DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED = 'entries.date_transaction_compensation';

    const TRANSACTION_TYPE_COLUMN = 'transaction_type';

    const TRANSACTION_TYPE_COLUMN_JOINED = 'entries.transaction_type';

    const PIX_TRANSACTION_TYPE = 'pix';

    const CASH_TRANSACTION_TYPE = 'cash';

    const ENTRIES_VALUE = 'entries';

    const DELETED_COLUMN = 'deleted';

    const DELETED_COLUMN_JOINED = 'entries.deleted';

    const REVIEWER_ID_COLUMN_JOINED = 'entries.reviewer_id';

    const COMPENSATED_COLUMN = 'transaction_compensation';

    const COMPENSATED_COLUMN_JOINED = 'entries.transaction_compensation';

    const COMPENSATED_VALUE = 'compensated';

    const TO_COMPENSATE_VALUE = 'to_compensate';

    const ID_COLUMN = 'id';

    const TIMESTAMP_VALUE_CPF_COLUMN = 'timestamp_value_cpf';

    const MEMBER_ID_COLUMN_JOINED = 'entries.member_id';

    const ACCOUNT_ID_COLUMN_JOINED = 'entries.account_id';
    const ACCOUNT_ID_COLUMN = 'account_id';

    const ACCOUNT_ID_COLUMN_JOINED_WITH_UNDERLINE = 'entries_account_id';

    const MEMBER_ID_COLUMN = 'member_id';

    const ID_COLUMN_JOINED = 'entries.id';

    const ENTRY_TYPE_COLUMN = 'entry_type';

    const ENTRY_TYPE_COLUMN_JOINED = 'entries.entry_type';

    const CULT_ID_COLUMN_JOINED = 'entries.cult_id';

    const ENTRY_TYPE_COLUMN_JOINED_WITH_UNDERLINE = 'entries_entry_type';

    const AMOUNT_COLUMN = 'amount';

    const AMOUNT_COLUMN_JOINED = 'entries.amount';

    const AMOUNT_COLUMN_WITH_ENTRIES_ALIAS = 'entries_amount';

    const AMOUNT_COLUMN_JOINED_WITH_UNDERLINE = 'entries_amount';

    const GROUP_RECEIVED_ID_COLUMN_JOINED = 'entries.group_received_id';

    const GROUP_RECEIVED_ID_COLUMN = 'group_received_id';

    const ENTRIES_AMOUNT_COLUMN_ALIAS = 'entries_amount';

    const DEVOLUTION_COLUMN = 'devolution';

    const DEVOLUTION_COLUMN_JOINED = 'entries.devolution';

    const TITHE_VALUE = 'tithe';

    const DESIGNATED_VALUE = 'designated';

    const OFFER_VALUE = 'offer';

    const ANONYMOUS_OFFERS_VALUE = 'anonymous_offers';

    const ACCOUNTS_TRANSFER_VALUE = 'accounts_transfer';

    const REGISTER_INDICATOR = 'register';

    const TRANSACTION_INDICATOR = 'transaction';

    const PAGINATE_NUMBER = 30;

    const ENTRY_TYPES_FILTER = 'entryTypes';

    const AMOUNT_FILTER = 'amount';

    const MEMBER_ID_FILTER = 'memberId';

    const RECIPIENT_FILTER = 'recipient';

    const GROUP_RECEIVED_ID_FILTER = 'groupReceivedId';

    const CUSTOM_DATES_FILTER = 'customDates';

    const TRANSACTION_TYPE_FILTER = 'transactionType';

    const MEMBERS_TYPE_FILTER = 'membersType';

    const MEMBERS_GENDERS_FILTER = 'membersGenders';

    const TITHES_NOT_IDENTIFIER_FILTER = 'tithesNotIdentifies';

    const EXCLUDED_ENTRIES_DUPLICATE_KEY = 'excluded';

    const KEPT_ENTRIES_DUPLICATE_KEY = 'kept';

    const DUP_ENTRY_TYPE_COLUMN = 'dup.entry_type';

    const DUP_AMOUNT_COLUMN = 'dup.amount';

    const DUP_TRANSACTION_TYPE_COLUMN = 'dup.transaction_type';

    const DUP_DATE_TRANSACTION_COLUMN = 'dup.date_transaction_compensation';

    const DUP_MEMBER_ID_COLUMN = 'dup.member_id';

    const DUP_REPETITION_COUNT_COLUMN = 'dup.repetition_count';

    const REPETITION_COUNT_COLUMN = 'repetition_count';

    const MEMBER_FULL_NAME_COLUMN = 'm.full_name';

    const GROUP_RECEIVED_NAME_COLUMN = 'g.name';

    // Aliases usados na query
    const MEMBER_FULL_NAME_ALIAS = 'member_full_name';

    const GROUP_RECEIVED_NAME_ALIAS = 'group_received_name';

    const GROUP_RETURNED_ID_ALIAS = 'group_returned_id';

    const GROUP_RECEIVED_ID_ALIAS = 'group_received_id';

    const DUPLICATE_IDS_ALIAS = 'duplicate_ids';

    const DUPLICITY_VERIFIED_COLUMN = 'duplicity_verified';

    const DUPLICITY_VERIFIED_COLUMN_JOINED = 'entries.duplicity_verified';

    const DUPLICATES_DISPLAY_SELECT_COLUMNS = [
        'dup.entry_type',
        'dup.amount',
        'dup.transaction_type',
        'dup.date_transaction_compensation',
        'dup.member_id',
        'MIN(m.full_name) AS member_full_name',
        'dup.repetition_count',
        'MIN(e.group_returned_id) AS group_returned_id',
        'MIN(e.group_received_id) AS group_received_id',
        'MIN(g.name) AS group_received_name',
        'JSON_ARRAYAGG(e.id) AS duplicate_ids',
    ];

    const DISPLAY_SELECT_COLUMNS = [
        'entries.id as entries_id',
        'entries.member_id as entries_member_id',
        'entries.account_id as entries_account_id',
        'entries.reviewer_id as entries_reviewer_id',
        'entries.cult_id as entries_cult_id',
        'entries.group_returned_id as entries_group_returned_id',
        'entries.group_received_id as entries_group_received_id',
        'entries.identification_pending as entries_identification_pending',
        'entries.entry_type as entries_entry_type',
        'entries.transaction_type as entries_transaction_type',
        'entries.transaction_compensation as entries_transaction_compensation',
        'entries.date_transaction_compensation as entries_date_transaction_compensation',
        'entries.date_entry_register as entries_date_entry_register',
        'entries.amount as entries_amount',
        'entries.timestamp_value_cpf as entries_timestamp_value_cpf',
        'entries.recipient as entries_recipient',
        'entries.duplicity_verified as entries_duplicity_verified',
        'entries.devolution as entries_devolution',
        'entries.residual_value as entries_residual_value',
        'entries.deleted as entries_deleted',
        'entries.comments as entries_comments',
        'entries.receipt_link as entries_receipt_link',
    ];

    const DISPLAY_SUM_AMOUNT_COLUMN = [
        'SUM(entries.amount) as tithe_amount',
    ];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];

    /**
     * @throws Throwable
     */
    public function newEntry(EntryData $entryData): Entry
    {
        return $this->create([
            'member_id' => $entryData->memberId,
            'account_id' => $entryData->accountId,
            'reviewer_id' => $entryData->reviewerId,
            'cult_id' => $entryData->cultId,
            'group_returned_id' => $entryData->groupReturnedId,
            'group_received_id' => $entryData->groupReceivedId,
            'identification_pending' => $entryData->identificationPending,
            'entry_type' => $entryData->entryType == 'tithes' ? 'tithe' : $entryData->entryType,
            'transaction_type' => $entryData->transactionType,
            'transaction_compensation' => $entryData->transactionCompensation,
            'date_transaction_compensation' => $entryData->dateTransactionCompensation,
            'date_entry_register' => $entryData->dateEntryRegister,
            'amount' => floatval($entryData->amount),
            'recipient' => $entryData->recipient,
            'duplicity_verified' => $entryData->duplicityVerified,
            'timestamp_value_cpf' => $entryData->timestampValueCpf,
            'devolution' => $entryData->devolution,
            'residual_value' => $entryData->residualValue,
            'deleted' => $entryData->deleted,
            'comments' => $entryData->comments,
            'receipt_link' => $entryData->receipt,
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getAllEntries(?string $dates): Collection
    {
        $this->queryConditions = [];
        $arrDates = explode(',', $dates);

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, false, 'and');

        if ($dates !== 'all') {
            $this->queryConditions[] = $this->whereLike(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, $arrDates, 'andWithOrInside');
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getDuplicitiesEntries(string $date): Collection
    {
        $query = function () use ($date) {
            $dupSub = DB::table(self::TABLE_NAME)
                ->select([
                    self::ENTRY_TYPE_COLUMN,
                    self::AMOUNT_COLUMN,
                    self::TRANSACTION_TYPE_COLUMN,
                    self::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                    self::MEMBER_ID_COLUMN,
                    DB::raw('COUNT(*) AS '.self::REPETITION_COUNT_COLUMN), // alias simples
                ])
                ->where(self::DELETED_COLUMN, 0)
                ->where(self::COMPENSATED_COLUMN, self::COMPENSATED_VALUE) // Laravel já coloca binding correto
                ->where(self::DUPLICITY_VERIFIED_COLUMN, 0)
                ->where(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$date}%")
                ->groupBy([
                    self::ENTRY_TYPE_COLUMN,
                    self::AMOUNT_COLUMN,
                    self::TRANSACTION_TYPE_COLUMN,
                    self::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                    self::MEMBER_ID_COLUMN,
                ])
                ->havingRaw('COUNT(*) > 1');

            $q = DB::table(self::TABLE_NAME.' AS e')
                ->select([
                    self::DUP_ENTRY_TYPE_COLUMN,
                    self::DUP_AMOUNT_COLUMN,
                    self::DUP_TRANSACTION_TYPE_COLUMN,
                    self::DUP_DATE_TRANSACTION_COLUMN,
                    self::DUP_MEMBER_ID_COLUMN,
                    DB::raw('MIN('.self::MEMBER_FULL_NAME_COLUMN.') AS '.self::MEMBER_FULL_NAME_ALIAS),
                    'dup.'.self::REPETITION_COUNT_COLUMN,
                    DB::raw('MIN(e.group_returned_id) AS '.self::GROUP_RETURNED_ID_ALIAS),
                    DB::raw('MIN(e.group_received_id) AS '.self::GROUP_RECEIVED_ID_ALIAS),
                    DB::raw('MIN('.self::GROUP_RECEIVED_NAME_COLUMN.') AS '.self::GROUP_RECEIVED_NAME_ALIAS),
                    DB::raw('MIN(e.'.self::DUPLICITY_VERIFIED_COLUMN.') AS '.self::DUPLICITY_VERIFIED_COLUMN),
                    DB::raw('JSON_ARRAYAGG(e.id) AS '.self::DUPLICATE_IDS_ALIAS),
                ])
                ->leftJoin(MemberRepository::TABLE_NAME.' AS m', 'e.member_id', '=', 'm.id')
                ->leftJoin(GroupsRepository::TABLE_NAME.' AS g', 'e.group_received_id', '=', 'g.id')
                ->joinSub($dupSub, 'dup', function ($join) {
                    $join->on(self::DUP_ENTRY_TYPE_COLUMN, '=', 'e.'.self::ENTRY_TYPE_COLUMN)
                        ->on(self::DUP_AMOUNT_COLUMN, '=', 'e.'.self::AMOUNT_COLUMN)
                        ->on(self::DUP_TRANSACTION_TYPE_COLUMN, '=', 'e.'.self::TRANSACTION_TYPE_COLUMN)
                        ->on(self::DUP_DATE_TRANSACTION_COLUMN, '=', 'e.'.self::DATE_TRANSACTIONS_COMPENSATION_COLUMN)
                        ->where(function ($cond) {
                            $cond->where(function ($q) {
                                $q->whereNull(self::DUP_MEMBER_ID_COLUMN)
                                    ->whereNull('e.'.self::MEMBER_ID_COLUMN);
                            })
                                ->orWhereColumn(self::DUP_MEMBER_ID_COLUMN, 'e.'.self::MEMBER_ID_COLUMN);
                        });
                })
                ->where('e.'.self::DELETED_COLUMN, 0)
                ->where('e.'.self::COMPENSATED_COLUMN, self::COMPENSATED_VALUE)
                ->where('e.'.self::DUPLICITY_VERIFIED_COLUMN, 0)
                ->where('e.'.self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$date}%")
                ->groupBy([
                    self::DUP_ENTRY_TYPE_COLUMN,
                    self::DUP_AMOUNT_COLUMN,
                    self::DUP_TRANSACTION_TYPE_COLUMN,
                    self::DUP_DATE_TRANSACTION_COLUMN,
                    self::DUP_MEMBER_ID_COLUMN,
                    'dup.'.self::REPETITION_COUNT_COLUMN,
                ])
                ->orderByDesc(self::DUP_DATE_TRANSACTION_COLUMN)
                ->orderByDesc(self::DUP_ENTRY_TYPE_COLUMN);

            $result = $q->get();

            return collect($result)->map(fn ($item) => DuplicityAnalisysEntriesData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getDevolutionEntries(?string $dates, bool $devolutionStatus = true, array $orderBy = [self::ID_COLUMN_JOINED]): Collection|Paginator
    {
        $this->queryConditions = [];
        $arrDates = explode(',', $dates);
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            MemberRepository::DISPLAY_SELECT_COLUMNS,
            FinancialReviewerRepository::DISPLAY_SELECT_COLUMNS
        );

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN_JOINED, false, 'and');
        $this->queryConditions[] = $this->whereEqual(self::DEVOLUTION_COLUMN_JOINED, $devolutionStatus, 'and');

        if ($dates !== 'all') {
            $this->queryConditions[] = $this->whereLike(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, $arrDates, 'andWithOrInside');
        }

        return $this->qbGetEntriesWithMembersAndReviewers(
            $this->queryConditions,
            $displayColumnsFromRelationship,
            $orderBy,
            false,
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function getAllEntriesWithMembersAndReviewers(
        ?string $dates,
        ?string $transactionCompensation,
        array $filters,
        array $orderBy,
        bool $paginate = true): Collection|Paginator
    {
        $arrDates = [];
        $this->queryConditions = [];
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            MemberRepository::DISPLAY_SELECT_COLUMNS,
            FinancialReviewerRepository::DISPLAY_SELECT_COLUMNS,
            AccountRepository::DISPLAY_SELECT_COLUMNS
        );

        if ($dates != 'all' && $dates != null) {
            $arrDates = explode(',', $dates);
        }

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN_JOINED, false, 'and');

        if ($transactionCompensation == 'compensated') {
            $this->queryConditions[] = $this->whereEqual(self::COMPENSATED_COLUMN_JOINED, self::COMPENSATED_VALUE, 'and');

            if ($dates !== 'all' && $dates != null) {
                $this->queryConditions[] = $this->whereLike(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED, $arrDates, 'andWithOrInside');
            }

            if ($dates == 'all') {
                $this->queryConditions[] = $this->whereEqual(self::COMPENSATED_COLUMN_JOINED, self::COMPENSATED_VALUE, 'and');
            }

            if (count($filters) > 0) {
                $this->applyFilters($filters, true);
            }

        } elseif ($transactionCompensation == 'to_compensate') {
            if ($dates !== 'all') {
                $this->queryConditions[] = $this->whereLike(self::DATE_ENTRY_REGISTER_COLUMN_JOINED, $arrDates, 'andWithOrInside');
                $this->queryConditions[] = $this->whereEqual(self::COMPENSATED_COLUMN_JOINED, self::TO_COMPENSATE_VALUE, 'andWithOrInside');
            } else {
                $this->queryConditions[] = $this->whereEqual(self::COMPENSATED_COLUMN_JOINED, self::TO_COMPENSATE_VALUE, 'and');
            }
        }

        return $this->qbGetEntriesWithMembersAndReviewers($this->queryConditions, $displayColumnsFromRelationship, $orderBy, $paginate);
    }

    /**
     * @return array|void
     */
    public function applyFilters(array $filters, bool $joinQuery = false, bool $returnConditions = false)
    {
        $this->queryConditions = count($this->queryConditions) > 0 ? $this->queryConditions : [];

        foreach ($filters as $key => $filter) {
            if ($key == self::ENTRY_TYPES_FILTER) {
                $entryTypes = explode(',', $filter);
                $column = $joinQuery ? self::ENTRY_TYPE_COLUMN_JOINED : self::ENTRY_TYPE_COLUMN;

                if (is_array($entryTypes)) {
                    $this->queryConditions[] = $this->whereEqual($column, $entryTypes, 'andWithOrInside');
                } else {
                    $this->queryConditions[] = $this->whereEqual($column, $filter, 'and');
                }
            }

            if ($key == self::GROUP_RECEIVED_ID_FILTER) {
                if ($filter != null) {
                    $column = $joinQuery ? self::GROUP_RECEIVED_ID_COLUMN_JOINED : self::GROUP_RECEIVED_ID_COLUMN;
                    $this->queryConditions[] = $this->whereEqual($column, [$filter, null], 'andWithOrInside');
                }
            }

            if ($key == self::CUSTOM_DATES_FILTER) {
                $column = $joinQuery ? self::DATE_TRANSACTIONS_COMPENSATION_COLUMN_JOINED : self::DATE_TRANSACTIONS_COMPENSATION_COLUMN;
                $this->queryConditions[] = $this->whereBetween($column, [$filter], 'and');
            }

            if ($key == self::TRANSACTION_TYPE_FILTER) {
                $transactionTypes = explode(',', $filter);
                $column = $joinQuery ? self::TRANSACTION_TYPE_COLUMN_JOINED : self::TRANSACTION_TYPE_COLUMN;

                if (is_array($transactionTypes)) {
                    $this->queryConditions[] = $this->whereEqual($column, $transactionTypes, 'andWithOrInside');
                } else {
                    $this->queryConditions[] = $this->whereEqual($column, $filter, 'and');
                }

            }

            if ($key == self::AMOUNT_FILTER) {
                $column = $joinQuery ? self::AMOUNT_COLUMN_JOINED : self::AMOUNT_COLUMN;
                $this->queryConditions[] = $this->whereEqual($column, floatval($filter), 'and');
            }

            if ($key == self::MEMBERS_TYPE_FILTER) {
                $membersTypes = explode(',', $filter);
                $column = $joinQuery ? MemberRepository::MEMBER_TYPE_COLUMN_JOINED : MemberRepository::MEMBER_TYPE_COLUMN;

                if (is_array($membersTypes)) {
                    $this->queryConditions[] = $this->whereEqual($column, $membersTypes, 'andWithOrInside');
                } else {
                    $this->queryConditions[] = $this->whereEqual($column, $filter, 'and');
                }
            }

            if ($key == self::MEMBERS_GENDERS_FILTER) {
                $membersGender = explode(',', $filter);
                $column = $joinQuery ? MemberRepository::MEMBER_GENDER_COLUMN_JOINED : MemberRepository::MEMBER_GENDER_COLUMN;

                if (is_array($membersGender)) {
                    $this->queryConditions[] = $this->whereEqual($column, $membersGender, 'andWithOrInside');
                } else {
                    $this->queryConditions[] = $this->whereEqual($column, $filter, 'and');
                }

            }

            if ($key == self::MEMBER_ID_FILTER) {
                $entryTypes = explode(',', $filters[self::ENTRY_TYPES_FILTER]);
                $column = $joinQuery ? self::MEMBER_ID_COLUMN_JOINED : self::MEMBER_ID_COLUMN;

                if (! array_key_exists(self::TITHES_NOT_IDENTIFIER_FILTER, $filters) and
                    (! in_array(self::DESIGNATED_VALUE, $entryTypes) and
                        ! in_array(self::OFFER_VALUE, $entryTypes))) {

                    $this->queryConditions[] = $this->whereEqual($column, [$filter], 'andWithOrInside');
                } else {
                    $this->queryConditions[] = $this->whereEqual($column, [$filter, null], 'andWithOrInside');
                }

            }

            if ($key == self::TITHES_NOT_IDENTIFIER_FILTER and $filter !== false) {
                $column = $joinQuery ? self::MEMBER_ID_COLUMN_JOINED : MemberRepository::ID_COLUMN;
                $this->queryConditions[] = $this->whereIsNull($column, 'and');
            }
        }

        if ($returnConditions) {
            return $this->queryConditions;
        }
    }

    /**
     * @throws BindingResolutionException
     */
    public function getAllEntriesByDateAndType(?string $date, string $dateType = 'register' | 'transaction', string $entryType = '*'): Collection
    {
        $this->requiredRelationships = ['member'];
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, false, 'and');

        if ($dateType == self::REGISTER_INDICATOR) {
            $this->queryConditions[] = $this->whereLike(self::DATE_ENTRY_REGISTER_COLUMN, $date, 'and');
        } elseif ($dateType == self::TRANSACTION_INDICATOR) {
            $this->queryConditions[] = $this->whereLike(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, $date, 'and');
        }

        if ($entryType != '*') {
            $this->queryConditions[] = $this->whereEqual(self::ENTRY_TYPE_COLUMN, $entryType, 'and');
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getEntryById(int $id): ?Model
    {
        $this->requiredRelationships = ['member'];
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::ID_COLUMN, $id, 'and');

        return $this->getItemWithRelationshipsAndWheres($this->queryConditions);
    }

    /**
     * @return Model|null
     *
     * @throws BindingResolutionException
     */
    public function getEntriesByCultId(int $id): ?Collection
    {
        $this->requiredRelationships = ['member', 'group'];
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::CULT_ID_COLUMN_JOINED, $id, 'and');

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }

    /**
     * @return Model|null
     *
     * @throws BindingResolutionException
     */
    public function getReceiptsEntriesByIds(array $ids): ?Collection
    {

        $query = function () use (
            $ids) {

            $q = DB::table(EntryRepository::TABLE_NAME)
                ->whereIn(self::ID_COLUMN_JOINED, $ids);

            $result = $q->get();

            return collect($result)->map(fn ($item) => ReceiptsByIdsData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getEntryByTimestampValueCpf(string $timestampValueCpf): ?Model
    {
        $this->requiredRelationships = ['member'];
        $this->queryConditions = [];

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, false, 'and');
        $this->queryConditions[] = $this->whereEqual(self::TIMESTAMP_VALUE_CPF_COLUMN, $timestampValueCpf, 'and');

        return $this->getItemWithRelationshipsAndWheres($this->queryConditions);
    }

    /**
     * @return bool
     *
     * @throws Throwable
     */
    public function updateEntry(int $id, EntryData $entryData): mixed
    {
        $conditions = ['field' => self::ID_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $id];

        return $this->update($conditions, [
            'member_id' => $entryData->memberId,
            'account_id' => $entryData->accountId,
            'reviewer_id' => $entryData->reviewerId,
            'group_returned_id' => $entryData->groupReturnedId,
            'group_received_id' => $entryData->groupReceivedId,
            'identification_pending' => $entryData->identificationPending,
            'entry_type' => $entryData->entryType == 'tithes' ? 'tithe' : $entryData->entryType,
            'transaction_type' => $entryData->transactionType,
            'transaction_compensation' => $entryData->transactionCompensation,
            'date_transaction_compensation' => $entryData->dateTransactionCompensation,
            'amount' => floatval($entryData->amount),
            'recipient' => $entryData->recipient,
            'timestamp_value_cpf' => $entryData->timestampValueCpf,
            'devolution' => $entryData->devolution,
            'residual_value' => $entryData->residualValue,
            'deleted' => $entryData->deleted,
            'comments' => $entryData->comments,
            'receipt_link' => $entryData->receipt,
        ]);
    }

    /**
     * @return bool
     *
     * @throws BindingResolutionException
     */
    public function updateIdentificationPending(int $entryId, int $identificationPending): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $entryId,
            ];

        return $this->update($conditions, [
            'identification_pending' => $identificationPending,
        ]);
    }

    /**
     * @return bool
     *
     * @throws BindingResolutionException
     */
    public function updateTimestampValueCpf(int $entryId, string $timestampValueCpf): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $entryId,
            ];

        return $this->update($conditions, [
            'timestamp_value_cpf' => $timestampValueCpf,
        ]);
    }

    /**
     * @return bool
     *
     * @throws BindingResolutionException
     */
    public function updateReceiptLink(int $entryId, string $receiptLink): mixed
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $entryId,
            ];

        return $this->update($conditions, [
            'receipt_link' => $receiptLink,
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function setDuplicityAnalysis(int $entryId): void
    {
        $conditions =
            [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $entryId,
            ];

        $this->update($conditions, [
            'duplicity_verified' => true,
        ]);
    }

    /**
     * @return mixed
     *
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
            'deleted' => 1,
        ]);
    }

    /**
     * Delete anonymous entries (anonymous_offers only) by account and reference date
     *
     * @param  string  $referenceDate  Format: Y-m
     *
     * @throws BindingResolutionException
     */
    public function deleteAnonymousEntriesByAccountAndDate(int $accountId, string $referenceDate): bool
    {
        $conditions = [
            [
                'field' => self::ACCOUNT_ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $accountId,
            ],
            [
                'field' => self::DATE_TRANSACTIONS_COMPENSATION_COLUMN,
                'operator' => BaseRepository::OPERATORS['LIKE'],
                'value' => "%{$referenceDate}%",
            ],
            [
                'field' => self::ENTRY_TYPE_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => self::ANONYMOUS_OFFERS_VALUE,
            ],
        ];

        return $this->update($conditions, [
            'deleted' => 1,
        ]);
    }

    /**
     * @return Collection
     *
     * @throws BindingResolutionException
     */
    public function getAmountByEntryType(string $dates, string $entryType = 'all'): mixed
    {
        $this->queryConditions = [];
        $arrDates = explode(',', $dates);

        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, false, 'and');

        if ($entryType != 'all') {
            $this->queryConditions[] = $this->whereEqual(self::ENTRY_TYPE_COLUMN, $entryType, 'and');
        }

        if ($dates !== 'all') {
            $this->queryConditions[] = $this->whereLike(self::DATE_TRANSACTIONS_COMPENSATION_COLUMN, $arrDates, 'andWithOrInside');
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
    }

    /*
    |------------------------------------------------------------------------------------------
    | Query Builder queries
    |------------------------------------------------------------------------------------------
    */

    /**
     * Get entries with members and reviewers joins
     *
     * @throws BindingResolutionException
     */
    public function qbGetEntriesWithMembersAndReviewers(
        array $queryClausesAndConditions,
        array $selectColumns,
        array $orderBy,
        bool $paginate = true,
        string $sort = 'desc'): Collection|Paginator
    {
        $query = function () use (
            $queryClausesAndConditions,
            $selectColumns,
            $orderBy,
            $paginate) {
            $q = DB::table(EntryRepository::TABLE_NAME)
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
                ->leftJoin(
                    AccountRepository::TABLE_NAME,
                    EntryRepository::ACCOUNT_ID_COLUMN_JOINED,
                    BaseRepository::OPERATORS['EQUALS'],
                    AccountRepository::ID_COLUMN_JOINED)
                ->where(function ($q) use ($queryClausesAndConditions) {
                    if (count($queryClausesAndConditions) > 0) {
                        foreach ($queryClausesAndConditions as $key => $clause) {
                            if ($clause['type'] == 'and') {
                                if ($clause['condition']['operator'] == BaseRepository::OPERATORS['LIKE']) {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], "%{$clause['condition']['value']}%");
                                }
                                if ($clause['condition']['operator'] == BaseRepository::OPERATORS['EQUALS']) {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                                }
                                if ($clause['condition']['operator'] == BaseRepository::OPERATORS['IS_NULL']) {
                                    $q->whereNull($clause['condition']['field']);
                                }
                                if ($clause['condition']['operator'] == BaseRepository::OPERATORS['BETWEEN']) {
                                    $arrDates = explode(',', $clause['condition']['value'][0]);
                                    $q->whereBetween($clause['condition']['field'], $arrDates);
                                }
                            }
                            if ($clause['type'] == 'andWithOrInside') {
                                $q->where(function ($query) use ($clause) {
                                    if (count($clause['condition']) > 0) {
                                        if ($clause['condition']['operator'] == BaseRepository::OPERATORS['EQUALS']) {
                                            foreach ($clause['condition']['value'] as $value) {
                                                $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], $value);
                                            }
                                        }
                                        if ($clause['condition']['operator'] == BaseRepository::OPERATORS['LIKE']) {
                                            foreach ($clause['condition']['value'] as $value) {
                                                $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], "%{$value}%");
                                            }
                                        }
                                        if ($clause['condition']['operator'] == BaseRepository::OPERATORS['IS_NULL']) {
                                            $query->orWhereNull($clause['condition']['field']);
                                        }
                                    }
                                });
                            }
                            if ($clause['type'] == 'or') {
                                $q->orWhere($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                            }
                            if ($clause['type'] == 'in') {
                                $q->whereIn($clause['condition']['field'], $clause['condition']['value']);
                            }
                            if ($clause['type'] == 'not_in') {
                                $q->whereNot($clause['condition']['field'], $clause['condition']['value']);
                            }
                        }
                    }
                });

            if (count($orderBy) > 0) {
                foreach ($orderBy as $clause) {
                    $q->orderByDesc($clause);
                }
            }

            if ($paginate) {
                return $q->simplePaginate(self::PAGINATE_NUMBER);
            } else {
                return $q->get();
            }
        };

        return $this->doQuery($query);
    }
}
