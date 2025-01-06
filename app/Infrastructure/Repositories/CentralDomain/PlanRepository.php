<?php

namespace Infrastructure\Repositories\CentralDomain;

use Domain\CentralDomain\Plans\Interfaces\PlanRepositoryInterface;
use Domain\CentralDomain\Plans\Models\Plan;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class PlanRepository extends BaseRepository implements PlanRepositoryInterface
{
    protected mixed $model = Plan::class;

    const PLAN_GOLD_NAME = 'gold';
    const PLAN_NAME_COLUMN = 'name';

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
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getPlans(): Collection
    {
        return tenancy()->central(function (){
            return $this->getItemsByWhere();
        });
    }


    /**
     * @param string $name
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getPlanByName(string $name): Model | null
    {
        return tenancy()->central(function () use ( $name) {
            return $this->getItemByColumn(
                self::PLAN_NAME_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                $name
            );
        });
    }
}
