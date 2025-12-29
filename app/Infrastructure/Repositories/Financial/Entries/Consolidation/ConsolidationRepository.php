<?php

namespace App\Infrastructure\Repositories\Financial\Entries\Consolidation;

use App\Domain\Financial\Entries\Consolidation\DataTransferObjects\ConsolidationEntriesData;
use App\Domain\Financial\Entries\Consolidation\Interfaces\ConsolidatedEntriesRepositoryInterface;
use App\Domain\Financial\Entries\Consolidation\Models\Consolidation;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class ConsolidationRepository extends BaseRepository implements ConsolidatedEntriesRepositoryInterface
{
    protected mixed $model = Consolidation::class;

    const TABLE_NAME = 'consolidation_entries';

    const DATE_COLUMN = 'date';

    const CONSOLIDATED_COLUMN = 'consolidated';

    const NOT_CONSOLIDATED_VALUE = '0';

    const CONSOLIDATED_VALUE = '1';

    const AMOUNT_TITHE_COLUMN = 'tithe_amount';

    const AMOUNT_DESIGNATED_COLUMN = 'designated_amount';

    const AMOUNT_OFFER_COLUMN = 'offers_amount';

    const AMOUNT_TOTAL_COLUMN = 'total_amount';

    /**
     * Array of conditions
     */
    private array $queryConditions = [];

    /**
     * @throws BindingResolutionException
     */
    public function getByDate(string $date): ?Model
    {
        $this->requiredRelationships = [];

        return $this->getItemByColumn(self::DATE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $date);
    }

    public function new(ConsolidationEntriesData $consolidationEntriesData): void
    {
        $this->create([
            'date' => substr($consolidationEntriesData->date, 0, 7),
            'consolidated' => $consolidationEntriesData->consolidated,
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getConsolidatedMonths(string|int $consolidated = 'all'): Collection
    {
        $this->queryConditions = [];

        if ($consolidated != 'all') {
            $this->queryConditions[] = $this->whereEqual(self::CONSOLIDATED_COLUMN, $consolidated, 'and');
        }

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions, self::DATE_COLUMN);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getEntriesEvolutionConsolidation(string $consolidatedValues, int $limit = 6): Collection
    {
        $query = DB::table(self::TABLE_NAME)
            ->orderBy(self::DATE_COLUMN, 'desc')
            ->limit($limit);

        if ($consolidatedValues != '*') {
            $query->where(self::CONSOLIDATED_COLUMN, $consolidatedValues);
        }

        return $query->get();
    }

    /**
     * @throws BindingResolutionException
     */
    public function updateConsolidationStatus(string $date, string $status): bool
    {
        return $this->update([
            'field' => 'date',
            'operator' => '=',
            'value' => $date,
        ], ['consolidated' => $status]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function checkConsolidationStatus(string $date): ?Model
    {
        $this->requiredRelationships = [];

        return $this->getItemByColumn(self::DATE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $date);
    }

    /**
     * @throws BindingResolutionException
     */
    public function updateTotalValueConsolidation(string $date, string $amount, string $column): bool
    {
        return $this->update([
            'field' => 'date',
            'operator' => '=',
            'value' => $date,
        ], [$column => $amount]);
    }

    public function deleteConsolidationEntry(string $date): bool
    {
        return $this->deleteByColumn(self::DATE_COLUMN, $date);
    }

    /**
     * Reopen a consolidated month by setting all relevant fields to 0
     *
     * @param  string  $month  Month in format YYYY-MM
     *
     * @throws BindingResolutionException
     */
    public function reopenConsolidatedMonth(string $month): bool
    {
        $conditions = [
            'field' => self::DATE_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $month,
        ];

        return $this->update($conditions, [
            'consolidated' => 0,
            'designated_amount' => 0,
            'offers_amount' => 0,
            'tithe_amount' => 0,
            'total_amount' => 0,
        ]);
    }
}
