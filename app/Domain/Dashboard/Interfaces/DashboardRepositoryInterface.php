<?php

namespace App\Domain\Dashboard\Interfaces;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function getActiveMembersCount(): int;

    public function getTotalEntries(string $month): float;

    public function getTotalPurchases(string $month): float;

    public function getTotalExits(string $month): float;

    public function getConsolidatedMonths(int $months): Collection;

    public function getEntriesByMonth(int $months): Collection;

    public function getExitsByMonth(int $months): Collection;
}
