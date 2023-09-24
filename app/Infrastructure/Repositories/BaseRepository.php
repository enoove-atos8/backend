<?php

namespace Infrastructure\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Infrastructure\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
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
      'MINOR' => '<',
      'MAJOR' => '>',
      'NOT_IN' => 'NOT IN',
      'IN' => 'IN',
      'NOT_NULL' => 'NOT NULL',
    ];



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
     * @var array
     */
    protected array $requiredRelationships = [];

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



    /**
     * Get all items
     *
     * @param string|null $columns specific columns to select
     * @param string $orderBy column to sort by
     * @param string $sort sort direction
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAll(string $columns = null, string $orderBy = 'created_at', string $sort = 'desc'): Collection
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
     * @param mixed $term search term
     * @param string $column column to search
     * @return Model
     * @throws BindingResolutionException
     */
    public function getItemByColumn(mixed $term, string $column = 'slug'): Model
    {
        $query = function () use ($term, $column) {
            return $this->model
                ->with($this->requiredRelationships)
                ->where($column, '=', $term)
                ->first();
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
     * Get instance of model by column
     *
     * @param array $queryClausesAndConditions
     * @param string $orderBy
     * @param string $sort
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getItemsWithRelationshipsAndWheres(
        array $queryClausesAndConditions,
        string $orderBy = 'id',
        string $sort = 'desc'): Collection
    {
        $query = function () use ($queryClausesAndConditions, $orderBy, $sort) {
            return $this->model
                ->with($this->requiredRelationships)
                ->where(function ($q) use($queryClausesAndConditions){
                    if($queryClausesAndConditions['where_clause']['exists'] and
                        count($queryClausesAndConditions['where_clause']['clause']) > 0){
                        foreach ($queryClausesAndConditions['where_clause']['clause'] as $key => $clause) {
                            if($clause['type'] == 'and'){
                                $q->where($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                            }
                            if($clause['type'] == 'andWithOrInside'){
                                $q->where(function($query) use($clause){
                                    if(count($clause['condition']) > 0){
                                        foreach ($clause['condition']['value'] as $value){
                                            $query->orWhere($clause['condition']['field'], $clause['condition']['operator'], "%{$value}%");
                                        }
                                    }
                                });
                            }
                            if($clause['type'] == 'or'){
                                $q->orWhere($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                            }
                            if($clause['type'] == 'in'){
                                $q->whereIn($clause['condition']['field'], $clause['condition']['operator'], $clause['condition']['value']);
                            }
                        }
                    }
                })
                ->orderBy($orderBy, $sort)
                ->get();
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
     * @param $id mixed primary key
     * @param $data array
     * @return mixed
     */
    public function update(mixed $id, array $data): mixed
    {
        return $this->model->where($this->model->getKeyName(), $id)->update($data);
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

        if (in_array(ThrowsHttpExceptions::class, $traits)) {

            if ($this->shouldThrowHttpException($result, $methodName)) {
                $this->throwNotFoundHttpException($methodName, $arguments);
            }

            $this->exceptionsDisabled = false;
        }

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
}
