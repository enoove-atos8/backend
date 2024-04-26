<?php

namespace Infrastructure\Repositories\Financial\Settings;

use App\Domain\Financial\Settings\Interfaces\FinancialSettingsRepositoryInterface;
use App\Domain\Financial\Settings\Models\FinancialSettings;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class FinancialSettingsRepository extends BaseRepository implements FinancialSettingsRepositoryInterface
{
    protected mixed $model = FinancialSettings::class;

    const TABLE_NAME = 'financial_settings';
    const ID_COLUMN = 'id';
    const MONTHLY_BUDGET_TITHES_COLUMN = 'monthly_budget_tithes';
    const BUDGET_ACTIVATED_COLUMN = 'budget_activated';

    const DISPLAY_SELECT_COLUMNS = [
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
     * @throws BindingResolutionException
     */
    public function getCurrentFinancialSettingsData(): Model
    {
        $conditions = [
            'field' => self::BUDGET_ACTIVATED_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => true,
        ];

        return $this->getItemWithRelationshipsAndWheres($conditions);
    }
}
