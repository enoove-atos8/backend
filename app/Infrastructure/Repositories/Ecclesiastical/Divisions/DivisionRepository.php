<?php

namespace Infrastructure\Repositories\Ecclesiastical\Divisions;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Divisions\Interfaces\DivisionRepositoryInterface;
use Domain\Ecclesiastical\Divisions\Models\Division;
use Faker\Provider\Base;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class DivisionRepository extends BaseRepository implements DivisionRepositoryInterface
{
    protected mixed $model = Division::class;
    const TABLE_NAME = 'ecclesiastical_divisions';
    const SLUG_COLUMN = 'slug';
    const ROUTE_RESOURCE_COLUMN = 'route_resource';
    const MINISTRIES_VALUE = 'ministries';
    const DEPARTURES_VALUE = 'departures';
    const ORGANIZATIONS_VALUE = 'organizations';
    const EDUCATION_VALUE = 'education';
    const ENABLED_COLUMN = 'enabled';
    const ID_COLUMN = 'ecclesiastical_divisions.id';
    const ID_COLUMN_JOINED = 'ecclesiastical_divisions.id';
    const NAME_COLUMN = 'ecclesiastical_divisions.name';

    const DISPLAY_SELECT_COLUMNS = [
        'ecclesiastical_divisions.id as division_id',
        'ecclesiastical_divisions.route_resource as division_slug',
        'ecclesiastical_divisions.name as division_name',
        'ecclesiastical_divisions.description as division_description',
        'ecclesiastical_divisions.enabled as division_enabled',
        'ecclesiastical_divisions.require_leader as require_leader',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param string $division
     * @return DivisionData|null
     * @throws UnknownProperties
     */
    public function getDivisionByName(string $division): ?DivisionData
    {
        $result = $this->model
            ->select(self::DISPLAY_SELECT_COLUMNS)
            ->where(
                self::ROUTE_RESOURCE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                $division
            )
            ->first();

        $attributes = $result->getAttributes();
        return DivisionData::fromResponse($attributes);
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
     * @param int $id
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getDivisionById(int $id): Model | null
    {
        return $this->getItemByColumn(
            self::ID_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            $id
        );
    }



    /**
     * @param DivisionData $divisionData
     * @return Division
     */
    public function createDivision(DivisionData $divisionData): Division
    {
        return $this->create([
            'slug'              =>   $divisionData->slug,
            'name'              =>   $divisionData->name,
            'description'       =>   $divisionData->description,
            'enabled'           =>   $divisionData->enabled,
            'require_leader'    =>   $divisionData->requireLeader,
        ]);
    }
}
