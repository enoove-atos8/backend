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
    const ENTRY_TYPE_COLUMN = 'entry_type';
    const AMOUNT_COLUMN = 'amount';

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
        $entry = $this->create([
            'entry_type'                     =>   $entryData->entryType,
            'transaction_type'               =>   $entryData->transactionType,
            'transaction_compensation'       =>   $entryData->transactionCompensation,
            'date_transaction_compensation'  =>   $entryData->dateTransactionCompensation,
            'date_entry_register'            =>   $entryData->dateEntryRegister,
            'amount'                         =>   floatval($entryData->amount),
            'recipient'                      =>   $entryData->recipient,
            'member_id'                      =>   $entryData->memberId,
            'reviewer_id'                    =>   $entryData->reviewerId,
        ]);


        throw_if(!$entry, GeneralExceptions::class, 'Houve um erro ao procesar o cadastro de uma nova igreja', 500);

        return $entry;
    }



    /**
     * @param string $rangeMonthlyDate
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAllEntries(string $rangeMonthlyDate): Collection
    {
        $this->requiredRelationships = ['member'];
        $whereConditions = [];
        $orWhereConditions = [];
        $dates = explode(',', $rangeMonthlyDate);

            foreach ($dates as $key => $month){
                if($key == 0)
                    $whereConditions[] = [self::DATE_ENTRY_REGISTER_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$month}%"];
                else
                    $orWhereConditions[] = [self::DATE_ENTRY_REGISTER_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$month}%"];
            }

        return $this->getItemsWithRelationshipsAndWheres($whereConditions, $orWhereConditions);
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
        $entry = $this->update($id, [
            'entry_type'                     =>   $entryData->entryType,
            'transaction_type'               =>   $entryData->transactionType,
            'transaction_compensation'       =>   $entryData->transactionCompensation,
            'date_transaction_compensation'  =>   $entryData->dateTransactionCompensation,
            'date_entry_register'            =>   $entryData->dateEntryRegister,
            'amount'                         =>   floatval($entryData->amount),
            'recipient'                      =>   $entryData->recipient,
            'member_id'                      =>   $entryData->memberId,
            'reviewer_id'                    =>   $entryData->reviewerId,
        ]);


        throw_if(!$entry, GeneralExceptions::class, 'Houve um erro ao procesar o cadastro de uma nova igreja', 500);

        return $entry;
    }


    /**
     * @param string $rangeMonthlyDate
     * @param string $amountType
     * @param string|null $entryType
     * @param string|null $exitType
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAmountByEntryType(string $rangeMonthlyDate, string $amountType, string $entryType = null, string $exitType = null): Collection
    {
        $whereConditions = [];
        $orWhereConditions = [];
        $dates = explode(',', $rangeMonthlyDate);

        $whereConditions[] = [self::ENTRY_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $entryType];

        foreach ($dates as $key => $month){
            if($key == 0)
            {
                $whereConditions[] = [self::DATE_ENTRY_REGISTER_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$month}%"];
            }
            else
            {
                $orWhereConditions[] = [self::DATE_ENTRY_REGISTER_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$month}%"];
                $whereConditions[] = [self::DATE_ENTRY_REGISTER_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$month}%"];
            }
        }

        return $this->getItemsWithRelationshipsAndWheres($whereConditions, $orWhereConditions);
    }



    /**
     * @param string $rangeMonthlyDate
     * @param string $amountType
     * @param string|null $entryType
     * @param string|null $exitType
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAmountByEntryTypeV2(string $rangeMonthlyDate, string $amountType, string $entryType = null, string $exitType = null): Collection
    {
        $indexRangeMonthlyDate = 0;
        $arrRangeMonthlyDate = explode(',', $rangeMonthlyDate);
        $lengthRangeMonthlyDate = $arrRangeMonthlyDate;
        $this->queryClausesAndConditions['where_clause']['exists'] = true;

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type' => 'and',
            'condition' => [
                'field' => self::ENTRY_TYPE_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $entryType,
            ]
        ];

        $this->queryClausesAndConditions['where_clause']['clause'][] = [
            'type'    =>  'andWithOrInside',
            'condition'   =>  [
                'field'   =>  self::DATE_ENTRY_REGISTER_COLUMN,
                'operator'   =>  BaseRepository::OPERATORS['LIKE'],
                'value'   =>  $arrRangeMonthlyDate,
            ]
        ];

        return $this->getItemsWithRelationshipsAndWheresV2($this->queryClausesAndConditions);
    }
}
