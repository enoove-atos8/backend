<?php

namespace Infrastructure\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * Get all items
     *
     * @param string|null $columns specific columns to select
     * @param string $orderBy column to sort by
     * @param string $sort sort direction
     */
    public function getAll(string $columns = null, string $orderBy = 'created_at', string $sort = 'desc');



    /**
     * Get paged items
     *
     * @param integer $paged Items per page
     * @param string $orderBy Column to sort by
     * @param string $sort Sort direction
     */
    public function getPaginated(int $paged = 15, string $orderBy = 'created_at', string $sort = 'desc');



    /**
     * Items for select options
     *
     * @param string $data column to display in the option
     * @param string $key column to be used as the value in option
     * @param string $orderBy column to sort by
     * @param string $sort sort direction
     * @return array           array with key value pairs
     */
    public function getForSelect(string $data, string $key = 'id', string $orderBy = 'created_at', string $sort = 'desc'): array;



    /**
     * Get item by its id
     *
     * @param  mixed $id
     */
    public function getById(mixed $id);



    /**
     * Get instance of model by column
     *
     * @param  mixed $term search term
     * @param string $column column to search
     */
    public function getItemByColumn(string $column, mixed $term);



    /**
     * Get a collection of items with conditions
     *
     * @param array $columns
     * @param array $conditions
     * @return Collection
     */
    public function getItemsByWhere(array $columns = ['*'], array $conditions = []): Collection;



    /**
     * Get an item with conditions
     *
     * @param array $columns
     * @param array $conditions
     * @return Model
     */
    public function getItemByWhere(array $columns = ['*'], array $conditions = []): Model;


    /**
     * Get instance of model by column
     *
     * @param array $queryClausesAndConditions
     * @param string $orderBy
     * @param string $sort
     * @return Collection
     */
    public function getItemsWithRelationshipsAndWheres(array $queryClausesAndConditions, string $orderBy = 'id', string $sort = 'desc'): Collection;



    /**
     * @param array $queryClausesAndConditions
     * @return Model|null
     */
    public function getItemWithRelationshipsAndWheres(array $queryClausesAndConditions): Model | null;



    /**
     * Get item by id or column
     *
     * @param  mixed $term id or term
     * @param string $column column to search
     */
    public function getActively(mixed $term, string $column = 'slug');



    /**
     * Create new using mass assignment
     *
     * @param array $data
     */
    public function create(array $data);



    /**
     * Update or crate a record and return the entity
     *
     * @param array $identifiers columns to search for
     * @param array $data
     */
    public function updateOrCreate(array $identifiers, array $data);



    /**
     * Delete a record by it's ID.
     *
     * @param $id
     * @return bool
     */
    public function delete($id): bool;
}
