<?php

namespace App\Domain\Dashboard\Interfaces;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function getActiveMembersCount(): int;

    public function getTotalTithes(string $month): float;

    public function getTotalOpenInvoices(): float;

    public function getTotalRealExits(string $month): float;

    public function getConsolidatedMonths(int $months): Collection;

    public function getEntriesByMonth(string $startMonth, string $endMonth): Collection;

    public function getExitsByMonth(string $startMonth, string $endMonth): Collection;

    public function getTotalActiveMembersByMonth(string $month): int;

    public function getActiveTithersByMonth(string $month): int;

    public function getTotalContributionsByMonth(string $month): int;
}
