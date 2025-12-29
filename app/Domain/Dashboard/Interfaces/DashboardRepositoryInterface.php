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

    public function getEntriesByMonth(int $months): Collection;

    public function getExitsByMonth(int $months): Collection;
}
