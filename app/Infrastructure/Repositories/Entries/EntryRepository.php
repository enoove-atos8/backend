<?php

namespace Infrastructure\Repositories\Entries;

use Domain\Entries\DataTransferObjects\EntryData;
use Domain\Entries\Interfaces\EntryRepositoryInterface;
use Domain\Entries\Models\Entry;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Throwable;
use function Webmozart\Assert\Tests\StaticAnalysis\nullOrCount;
use function Webmozart\Assert\Tests\StaticAnalysis\string;

class EntryRepository extends BaseRepository implements EntryRepositoryInterface
{
    protected mixed $model = Entry::class;
    const DATE_ENTRY_REGISTER_COLUMN = 'date_entry_register';
    const DELETED_COLUMN = 'deleted';
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const AMOUNT_COLUMN = 'amount';
    const DEVOLUTION_COLUMN = 'devolution';

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
        ]);
    }



    /**
     * @param string $rangeMonthlyDate
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAllEntries(string $rangeMonthlyDate): Collection
    {
        $arrRangeMonthlyDate = explode(',', $rangeMonthlyDate);
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->requiredRelationships = ['member'];


        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => ['field' => self::DELETED_COLUMN, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => false,]
        ];

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'andWithOrInside',
            'condition' => ['field' => self::DATE_ENTRY_REGISTER_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]
        ];


        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }



    /**
     * @param int $id
     * @return Model
     * @throws BindingResolutionException
     */
    public function getEntryById(int $id): Model
    {
        $this->requiredRelationships = ['member'];

        return $this->getById($id);
    }



    /**
     * @param int $id
     * @param EntryData $entryData
     * @return bool
     * @throws Throwable
     */
    public function updateEntry(int $id, EntryData $entryData): bool
    {
        return $this->update($id, [
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
        $this->queryClausesAndConditions['where_clause']['clause'][] =
            ['type' => 'andWithOrInside', 'condition' =>  ['field' => self::DATE_ENTRY_REGISTER_COLUMN, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $arrRangeMonthlyDate,]];

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }
}
