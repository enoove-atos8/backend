<?php

namespace Infrastructure\Repositories\Ecclesiastical\Divisions;

use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Faker\Provider\Base;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class DivisionRepository extends BaseRepository implements DivisionRepositoryInterface
{
    protected mixed $model = Division::class;
    const TABLE_NAME = 'ecclesiastical_division';
    const ROUTE_RESOURCE_COLUMN = 'route_resource';
    const ENABLED_COLUMN = 'enabled';
    const ID_COLUMN = 'id';


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


    /**
     * @param int $enabled
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getDivisions(int $enabled): Collection
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::ENABLED_COLUMN, $enabled, 'and');

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN,
            BaseRepository::ORDERS['ASC']
        );
    }


    /**
     * @param string $division
     * @return Model
     * @throws BindingResolutionException
     */
    public function getDivisionIdByName(string $division): Model
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::ROUTE_RESOURCE_COLUMN, $division, 'and');

        return $this->getItemWithRelationshipsAndWheres(
            $this->queryConditions
        );
    }
}
