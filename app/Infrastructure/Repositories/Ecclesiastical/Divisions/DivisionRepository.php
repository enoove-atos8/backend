<?php

namespace Infrastructure\Repositories\Ecclesiastical\Divisions;

use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\BaseRepository;

class DivisionRepository extends BaseRepository implements DivisionRepositoryInterface
{
    protected mixed $model = Division::class;
    const TABLE_NAME = 'ecclesiastical_division';
    const ROUTE_RESOURCE_COLUMN = 'route_resource';


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @throws BindingResolutionException
     */
    public function getDivisionByName(string $division): Model
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::ROUTE_RESOURCE_COLUMN, $division, 'and');

        return $this->getItemWithRelationshipsAndWheres($this->queryConditions);
    }
}
