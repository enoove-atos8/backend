<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Throwable;

class GetExitsAction
{
    private ExitRepository $exitRepository;

    public function __construct(
        ExitRepositoryInterface $exitRepositoryInterface,
    )
    {
        $this->exitRepository = $exitRepositoryInterface;
    }

    /**
     * @throws Throwable
     */
    public function execute(null | string $dates, array $filters): Collection | Paginator
    {
        return $this->exitRepository->getExits(
            $dates,
            $filters
        );
    }
}
