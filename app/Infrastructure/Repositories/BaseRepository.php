<?php

namespace Infrastructure\Repositories;

use App\Infrastructure\Repositories\Financial\Entries\General\EntryRepository;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\Member\MemberRepository;
use Infrastructure\Traits\Repositories\CacheResults;
use Infrastructure\Traits\Repositories\ThrowsHttpExceptions;
use PhpParser\Node\Expr\AssignOp\Mod;

abstract class BaseRepository implements BaseRepositoryInterface
{
    use ThrowsHttpExceptions, CacheResults;

    const OPERATORS = [
      'LIKE' => 'LIKE',
      'BETWEEN' => 'BETWEEN',
      'EQUALS' => '=',
      'NOT_EQUALS' => '<>',
      'DIFFERENT' => '!=',
      'MINOR' => '<',
      'MAJOR' => '>',
      'NOT_IN' => 'NOT IN',
      'IN' => 'IN',
      'NOT_NULL' => 'NOT NULL',
      'IS_NULL' => 'IS NULL',
      'IS' => 'IS',
    ];

    const ORDERS = [
        'ASC' => 'ASC',
        'DESC' => 'DESC',
    ];

    const ID_COLUMN = 'id';
    const LIMIT_ENTRIES_DATA = '1000';
    const ALL_DATA_SELECT = ['entries.id'];


    //public array $queryConditions = [];

    /**
     * Name of model associated with this repository
     * @var Model
     */
    protected mixed $model;

    /**
     * Array of method names of relationships available to use
     * @var array
     */
    protected array $relationships = [];

    /**
     * Array of relationships to include in next query
     * @var string|array
     */
    protected string|array $requiredRelationships = [];

    /**
     * Array of traits being used by the repository.
     * @var array
     */
    protected array $uses = [];

    protected int $cacheTtl = 1;

    protected bool $caching = false;

    /**
     * Get the model from the IoC container
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->model = app()->make($this->model);
        $this->setUses();
    }

    /*
    |------------------------------------------------------------------------------------------
    |
    | GENERAL QUERIES
    | Description: These queries run simple actions on database without
    |               relationships in data returned
    |
    |------------------------------------------------------------------------------------------
    */

    /**
     * Get all items
     *
     * @param string $columns specific columns to select
     * @param string $orderBy column to sort by
     * @param string $sort sort direction
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAll(string $columns = '*', string $orderBy = 'created_at', string $sort = 'desc'): Collection
    {
        $query = function () use ($columns, $orderBy, $sort) {

            return $this->model
                ->with($this->requiredRelationships)
                ->orderBy($orderBy, $sort)
                ->get($columns);
        };

        return $this->doQuery($query);
    }



    /**
     * Get paged items
     *
     * @param integer $paged Items per page
     * @param string $orderBy Column to sort by
     * @param string $sort Sort direction
     * @return Paginator
     * @throws BindingResolutionException
     */
    public function getPaginated(int $paged = 15, string $orderBy = 'created_at', string $sort = 'desc'): Paginator
    {
        $query = function () use ($paged, $orderBy, $sort) {

            return $this->model
                ->with($this->requiredRelationships)
                ->orderBy($orderBy, $sort)
                ->paginate($paged);
        };

        return $this->doQuery($query);
    }



    /**
     * Items for select options
     *
     * @param string $data column to display in the option
     * @param string $key column to be used as the value in option
     * @param string $orderBy column to sort by
     * @param string $sort sort direction
     * @return array           array with key value pairs
     * @throws BindingResolutionException
     */
    public function getForSelect(string $data, string $key = 'id', string $orderBy = 'created_at', string $sort = 'desc'): array
    {
        $query = function () use ($data, $key, $orderBy, $sort) {
            return $this->model
                ->with($this->requiredRelationships)
                ->orderBy($orderBy, $sort)
                ->lists($data, $key)
                ->all();
        };

        return $this->doQuery($query);
    }



    /**
     * Get item by its id
     *
     * @param integer $id
     * @return Model
     * @throws BindingResolutionException
     */
    public function getById($id): Model
    {
        $query = function () use ($id) {
            return $this->model
                ->with($this->requiredRelationships)
                ->find($id);
        };

        return $this->doQuery($query);
    }



    /**
     * Get instance of model by column
     *
     * @param string $column column to search
     * @param mixed $term search term
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getItemByColumn(string $column, mixed $term): Model|null
    {
        $query = function () use ($column, $term) {
            return $this->model
                ->with($this->requiredRelationships)
                ->where($column, '=', $term)
                ->first();
        };

        return $this->doQuery($query);
    }



    /**
     * Get instance of model by column
     *
     * @param string $column column to search
     * @param mixed $term search term
     * @param string $orderByCollumn
     * @param string $sortType
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getItemsByColumn(string $column, mixed $term, string $orderByCollumn = 'id', string $sortType = 'desc'): Collection
    {
        $query = function () use ($column, $term, $orderByCollumn, $sortType) {
            return $this->model
                ->with($this->requiredRelationships)
                ->where($column, '=', $term)
                ->whereNot('', '', '')
                ->orderBy($orderByCollumn, $sortType)
                ->get();
        };

        return $this->doQuery($query);
    }



    /**
     * Get a collection of items with conditions
     *
     * @param array $columns
     * @param array $conditions
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getItemsByWhere(array $columns = ['*'], array $conditions = []): Collection
    {
        $query = function () use ($columns, $conditions) {
            return DB::table($this->model->getTable())
                ->select($columns)
                ->where($conditions)
                ->get();
        };

        return $this->doQuery($query);
    }



    /**
     * Get an item with conditions
     *
     * @param array $columns
     * @param array $conditions
     * @return Model
     * @throws BindingResolutionException
     */
    public function getItemByWhere(array $columns = ['*'], array $conditions = []): Model
    {
        $query = function () use ($columns, $conditions) {
            return DB::table($this->model->getTable())
                ->select($columns)
                ->where($conditions)
                ->first();
        };

        return $this->doQuery($query);
    }



    /**
     * Get item by id or column
     *
     * @param mixed $term id or term
     * @param string $column column to search
     * @return Model
     * @throws BindingResolutionException
     */
    public function getActively(mixed $term, string $column = 'slug'): Model
    {
        if (is_numeric($term)) {
            return $this->getById($term);
        }

        return $this->getItemByColumn($term, $column);
    }



    /**
     * Create new using mass assignment
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }



    /**
     * Update a record using the primary key.
     *
     * @param array $conditions
     * @param $data array
     * @return mixed
     * @throws BindingResolutionException
     */
    public function update(array $conditions, array $data): mixed
    {
        $query = function () use ($conditions, $data) {
            return $this->model
                ->with($this->requiredRelationships)
                ->where($conditions['field'], $conditions['operator'], $conditions['value'])
                ->update($data);
        };

        return $this->doQuery($query);
    }



    /**
     * Update or crate a record and return the entity
     *
     * @param array $identifiers columns to search for
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $identifiers, array $data): mixed
    {
        $existing = $this->model->where(array_only($data, $identifiers))->first();

        if ($existing) {
            $existing->update($data);

            return $existing;
        }

        return $this->create($data);
    }



    /**
     * Delete a record by the primary key.
     *
     * @param $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->model->where($this->model->getKeyName(), $id)->delete();
    }



    /**
     * Delete a record by the column specified.
     *
     * @param string $column
     * @param string $data
     * @return bool
     */
    public function deleteByColumn(string $column, string $data): bool
    {
        return $this->model->where($column, $data)->delete();
    }


    /*
    |------------------------------------------------------------------------------------------
    |
    | CUSTOM QUERIES
    | Description: These queries run custom actions on database
    |               with relationships in data returned
    |
    |------------------------------------------------------------------------------------------
    */


    /**
     * Choose what relationships to return with query.
     *
     * @param mixed $relationships
     * @return $this
     */
    public function with(mixed $relationships): static
    {
        $this->requiredRelationships = [];

        if ($relationships == 'all') {
            $this->requiredRelationships = $this->relationships;
        } elseif (is_array($relationships)) {
            $this->requiredRelationships = array_filter($relationships, function ($value) {
                return in_array($value, $this->relationships);
            });
        } elseif (is_string($relationships)) {
            array_push($this->requiredRelationships, $relationships);
        }

        return $this;
    }


    /**
     * Get instance of model by column
     *
     * @param array $queryClausesAndConditions
     * @param string $orderBy
     * @param string $sort
     * @param string $limit
     * @param array $selectColumns
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getItemsWithRelationshipsAndWheres(
        array $queryClausesAndConditions,
        string $orderBy = 'id',
        string $sort = 'desc',
        string $limit = '1001',
        array $selectColumns = ['*']): Collection
    {
        $query = function () use ($queryClausesAndConditions, $orderBy, $sort, $limit, $selectColumns) {
            return $this->model
                ->with($this->requiredRelationships)
                ->where(function ($q) use($queryClausesAndConditions){
                    if(count($queryClausesAndConditions) > 0){
                        foreach ($queryClausesAndConditions as $key => $clause) {
                            if($clause['type'] == 'and'){
                                if($clause['condition']['operator'] == BaseRepository::OPERATORS['LIKE'])
                                {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], "%{$clause['condition']['value']}%");
                                }
                                if($clause['condition']['operator'] == BaseRepository::OPERATORS['EQUALS'])
                                {
                                    $q->where($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                                }
                                if($clause['condition']['operator'] == BaseRepository::OPERATORS['IS_NULL'])
                                {
                                    $q->whereNull($clause['condition']['field']);
                                }
                            }
                            if($clause['type'] == 'andWithOrInside')
                            {
                                $q->where(function($query) use($clause)
                                {
                                    if(count($clause['condition']) > 0)
                                    {
                                        if($clause['condition']['operator'] == BaseRepository::OPERATORS['EQUALS'])
                                        {
                                            foreach ($clause['condition']['value'] as $value)
                                            {
                                                $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], $value);
                                            }
                                        }
                                        if($clause['condition']['operator'] == BaseRepository::OPERATORS['LIKE'])
                                        {
                                            foreach ($clause['condition']['value'] as $value){
                                                $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], "%{$value}%");
                                            }
                                        }
                                        if($clause['condition']['operator'] == BaseRepository::OPERATORS['IS_NULL'])
                                        {
                                            $query->orWhereNull($clause['condition']['field']);
                                        }
                                    }
                                });
                            }
                            if($clause['type'] == 'or'){
                                $q->orWhere($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                            }
                            if($clause['type'] == 'in'){
                                $q->whereIn($clause['condition']['field'], $clause['condition']['value']);
                            }
                            if($clause['type'] == 'not_in'){
                                $q->whereNot($clause['condition']['field'], $clause['condition']['value']);
                            }
                        }
                    }
                })
                ->orderBy($orderBy, $sort)
                ->limit($limit)
                ->select($selectColumns)
                ->get();
        };

        return $this->doQuery($query);
    }


    /**
     * Get instance of model by column
     *
     * @param array $queryClausesAndConditions
     * @param string $orderByColumn
     * @param array $selectColumns
     * @param int $limit
     * @param string $orderDirection
     * @return Model|null
     * @throws BindingResolutionException
     */
    public function getItemWithRelationshipsAndWheres(
        array $queryClausesAndConditions,
        string $orderByColumn = 'id',
        array $selectColumns = ['*'],
        int $limit = 1000,
        string $orderDirection = 'desc'): Model | null
    {
        $query = function () use ($queryClausesAndConditions, $orderByColumn, $orderDirection, $limit, $selectColumns) {
            return $this->model
                ->with($this->requiredRelationships)
                ->where(function ($q) use($queryClausesAndConditions){
                    foreach ($queryClausesAndConditions as $condition)
                    {
                        $c = $condition['condition'];
                        $q->where($c['field'], $c['operator'], $c['value']);
                    }
                })
                ->limit($limit)
                ->select($selectColumns)
                ->orderBy($orderByColumn, $orderDirection)
                ->first();
        };

        return $this->doQuery($query);
    }



    /**
     * Perform the repository query.
     *
     * @param $callback
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function doQuery($callback): mixed
    {
        $previousMethod = debug_backtrace(null, 2)[1];
        $methodName = $previousMethod['function'];
        $arguments = $previousMethod['args'];

        $result = $this->doBeforeQuery($callback, $methodName, $arguments);

        return $this->doAfterQuery($result, $methodName, $arguments);
    }



    /**
     *  Apply any modifiers to the query.
     *
     * @param $callback
     * @param $methodName
     * @param $arguments
     * @return mixed
     * @throws BindingResolutionException
     */
    private function doBeforeQuery($callback, $methodName, $arguments): mixed
    {
        $traits = $this->getUsedTraits();

        if (in_array(CacheResults::class, $traits) && $this->caching && $this->isCacheableMethod($methodName)) {
            return $this->processCacheRequest($callback, $methodName, $arguments);
        }

        return $callback();
    }

    /**
     * Handle the query result.
     *
     * @param $result
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    private function doAfterQuery($result, $methodName, $arguments): mixed
    {
        $traits = $this->getUsedTraits();

        if (in_array(CacheResults::class, $traits)) {
            // Reset caching to enabled in case it has just been disabled.
            $this->caching = true;
        }

        /*if (in_array(ThrowsHttpExceptions::class, $traits)) {

            if ($this->shouldThrowHttpException($result, $methodName)) {
                $this->throwNotFoundHttpException($methodName, $arguments);
            }

            $this->exceptionsDisabled = false;
        }*/

        return $result;
    }

    /**
     * @return int
     */
    protected function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * @return $this
     */
    protected function setUses(): static
    {
        $this->uses = array_flip(class_uses_recursive(get_class($this)));

        return $this;
    }

    /**
     * @return array
     */
    protected function getUsedTraits(): array
    {
        return $this->uses;
    }


    /*
    |------------------------------------------------------------------------------------------
    |
    | SQL Clauses functions
    | Description: These functions represents clauses sql as where
    |
    |------------------------------------------------------------------------------------------
    */


    /**
     * @param string $column
     * @param mixed $value
     * @param string $whereType
     * @return array
     */
    public function whereEqual(string $column, mixed $value, string $whereType): array
    {
        return [
                'type' => $whereType,
                'condition' => ['field' => $column, 'operator' => BaseRepository::OPERATORS['EQUALS'], 'value' => $value,]
        ];
    }


    /**
     * @param string $column
     * @param mixed $value
     * @param string $whereType
     * @return array
     */
    public function whereLike(string $column, mixed $value, string $whereType): array
    {
        return [
            'type' => $whereType,
            'condition' => ['field' => $column, 'operator' => BaseRepository::OPERATORS['LIKE'], 'value' => $value,]
        ];
    }


    /**
     * @param string $column
     * @param string $whereType
     * @return array
     */
    public function whereIsNull(string $column,  string $whereType): array
    {
        return [
            'type' => $whereType,
            'condition' => ['field' => $column, 'operator' => BaseRepository::OPERATORS['IS_NULL']]
        ];
    }


    /**
     * @param string $column
     * @param mixed $value
     * @param string $whereType
     * @return array
     */
    public function whereNotIn(string $column, mixed $value, string $whereType): array
    {
        return [
            'type' => $whereType,
            'condition' => ['field' => $column, 'operator' => BaseRepository::OPERATORS['NOT_IN'], 'value' => $value,]
        ];
    }
}
