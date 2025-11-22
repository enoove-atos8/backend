<?php

namespace Infrastructure\Repositories\CentralDomain;

use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Domain\CentralDomain\Plans\Interfaces\PlanRepositoryInterface;
use Domain\CentralDomain\Plans\Models\Plan;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class PlanRepository extends BaseRepository implements PlanRepositoryInterface
{
    protected mixed $model = Plan::class;

    const TABLE_NAME = 'plans';

    const ID_COLUMN = 'id';

    const PLAN_GOLD_NAME = 'gold';

    const PLAN_DIAMOND_NAME = 'diamond';

    const PLAN_NAME_COLUMN = 'name';

    const ACTIVATED_COLUMN = 'activated';

    /**
     * Array of where, between and another clauses that was mounted dynamically
     */
    private array $queryClausesAndConditions = [
        'where_clause' => [
            'exists' => false,
            'clause' => [],
        ],
    ];

    /**
     * @throws UnknownProperties
     */
    public function getPlans(): Collection
    {
        return tenancy()->central(function () {
            $results = DB::table(self::TABLE_NAME)
                ->where(self::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], true)
                ->get();

            return $results->map(function ($result) {
                return PlanData::fromResponse((array) $result);
            });
        });
    }

    /**
     * @throws BindingResolutionException
     */
    public function getPlanByName(string $name): ?Model
    {
        return tenancy()->central(function () use ($name) {
            return $this->getItemByColumn(
                self::PLAN_NAME_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                $name
            );
        });
    }

    /**
     * Get plan by ID and return as PlanData
     *
     * @throws BindingResolutionException
     * @throws UnknownProperties
     */
    public function getPlanById(int $id): ?PlanData
    {
        return tenancy()->central(function () use ($id) {
            $result = DB::table(self::TABLE_NAME)
                ->where(self::ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $id)
                ->first();

            return $result ? PlanData::fromResponse((array) $result) : null;
        });
    }
}
