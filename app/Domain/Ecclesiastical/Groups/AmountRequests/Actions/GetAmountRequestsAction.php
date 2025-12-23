<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetAmountRequestsAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all amount requests with optional filters (paginated)
     *
     * @param  array  $filters  Optional filters (status, group_id, member_id, date_from, date_to)
     * @param  bool  $paginate  Whether to paginate results
     */
    public function execute(array $filters = [], bool $paginate = true): Collection|Paginator
    {
        return $this->repository->getAll($filters, $paginate);
    }
}
