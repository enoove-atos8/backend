<?php

namespace Infrastructure\Repositories\Financial\Settings;

use App\Domain\Financial\Settings\DataTransferObjects\FinancialSettingsData;
use App\Domain\Financial\Settings\Interfaces\FinancialSettingsRepositoryInterface;
use App\Domain\Financial\Settings\Models\FinancialSettings;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class FinancialSettingsRepository extends BaseRepository implements FinancialSettingsRepositoryInterface
{
    protected mixed $model = FinancialSettings::class;

    const TABLE_NAME = 'financial_settings';
    const ID_COLUMN = 'id';
    const BUDGET_VALUE_COLUMN = 'budget_value';
    const BUDGET_TYPE_COLUMN = 'budget_type';
    const BUDGET_ACTIVATED_COLUMN = 'budget_activated';

    const BUDGET_TYPE_TITHES = 'tithes';
    const BUDGET_TYPE_EXITS = 'exits';

    const DISPLAY_SELECT_COLUMNS = [];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];

    /**
     * @throws BindingResolutionException
     */
    public function getSettingsByType(string $budgetType): Model|null
    {
        $this->queryConditions = [];
        $this->queryConditions[] = $this->whereEqual(self::BUDGET_TYPE_COLUMN, $budgetType, 'and');

        return $this->getItemWithRelationshipsAndWheres($this->queryConditions);
    }

    public function saveSettings(FinancialSettingsData $data): bool
    {
        $existing = DB::table(self::TABLE_NAME)
            ->where(self::BUDGET_TYPE_COLUMN, $data->budgetType)
            ->first();

        $settingsData = [
            self::BUDGET_VALUE_COLUMN => $data->budgetValue,
            self::BUDGET_TYPE_COLUMN => $data->budgetType,
            self::BUDGET_ACTIVATED_COLUMN => $data->budgetActivated,
            'updated_at' => now(),
        ];

        if ($existing) {
            return DB::table(self::TABLE_NAME)
                ->where(self::ID_COLUMN, $existing->id)
                ->update($settingsData) >= 0;
        }

        $settingsData['created_at'] = now();

        return DB::table(self::TABLE_NAME)->insert($settingsData);
    }
}
