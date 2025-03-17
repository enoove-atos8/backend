<?php

namespace Domain\Financial\Exits\Exits\Interfaces;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;

interface ExitRepositoryInterface
{
    public function getExits(?string $dates, array $filters, string $transactionCompensation = ExitRepository::COMPENSATED_VALUE, bool $paginate = true): Collection | Paginator;
}
