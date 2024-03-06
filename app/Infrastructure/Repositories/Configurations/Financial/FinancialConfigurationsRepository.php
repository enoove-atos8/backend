<?php

namespace Infrastructure\Repositories\Configurations\Financial;

use Domain\Configurations\Financial\Interfaces\FinancialConfigurationRepositoryInterface;
use Domain\Configurations\Financial\Models\FinancialConfigurations;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class FinancialConfigurationsRepository extends BaseRepository implements FinancialConfigurationRepositoryInterface
{
    protected mixed $model = FinancialConfigurations::class;

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
    public function getFinancialConfigurationData(): Collection
    {
        $this->queryClausesAndConditions['where_clause']['exists'] = false;

        return $this->getItemsWithRelationshipsAndWheres($this->queryClausesAndConditions);
    }
}
