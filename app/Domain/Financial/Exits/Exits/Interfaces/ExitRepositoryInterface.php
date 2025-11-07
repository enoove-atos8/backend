<?php

namespace Domain\Financial\Exits\Exits\Interfaces;

use Domain\Financial\Exits\Exits\DataTransferObjects\ExitData;
use Domain\Financial\Exits\Exits\Models\Exits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface ExitRepositoryInterface
{
    public function getExits(?string $dates, array $filters, array $orderBy, string $transactionCompensation, bool $paginate = true, bool $queryOnlyExitsTable = false): Collection|Paginator;

    public function getAmountByExitType(string $dates, string $exitType = '*'): mixed;

    public function getExitByTimestamp(string $timestamp): ?Model;

    public function getExitById(int $exitId): ?ExitData;

    public function newExit(ExitData $exitData): Exits;

    public function updateExit(int $exitId, ExitData $exitData): bool;

    public function deleteExit(int $id): bool;

    public function updateTimestamp(int $exitId, string $timestamp): mixed;

    public function updateReceiptLink(int $exitId, string $link): mixed;

    public function deleteAnonymousExitsByAccountAndDate(int $accountId, string $referenceDate): bool;
}
