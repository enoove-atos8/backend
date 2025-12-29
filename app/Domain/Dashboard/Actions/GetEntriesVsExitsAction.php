<?php

namespace App\Domain\Dashboard\Actions;

use App\Domain\Dashboard\DataTransferObjects\EntriesVsExitsData;
use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use Carbon\Carbon;

class GetEntriesVsExitsAction
{
    private const MONTHS_ABBR_PT_BR = [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez',
    ];

    public function __construct(
        private DashboardRepositoryInterface $repository
    ) {}

    public function execute(int $months): EntriesVsExitsData
    {
        // Buscar meses consolidados como base
        $consolidatedMonths = $this->repository->getConsolidatedMonths($months);

        // Buscar dízimos e saídas reais
        $entriesData = $this->repository->getEntriesByMonth($months);
        $exitsData = $this->repository->getExitsByMonth($months);

        // Mapear entradas (dízimos) por mês
        $entriesMap = [];
        foreach ($entriesData as $entry) {
            $entriesMap[$entry->month] = (float) $entry->total;
        }

        // Mapear saídas por mês
        $exitsMap = [];
        foreach ($exitsData as $exit) {
            $exitsMap[$exit->month] = (float) $exit->total;
        }

        // Usar os meses consolidados como base (do mais antigo ao mais recente)
        $sortedMonths = $consolidatedMonths->sort()->values();

        $categories = [];
        $entries = [];
        $exits = [];

        foreach ($sortedMonths as $monthKey) {
            $monthNumber = (int) Carbon::parse($monthKey.'-01')->format('m');

            $categories[] = self::MONTHS_ABBR_PT_BR[$monthNumber];
            $entries[] = $entriesMap[$monthKey] ?? 0;
            $exits[] = $exitsMap[$monthKey] ?? 0;
        }

        $totalEntries = array_sum($entries);
        $totalExits = array_sum($exits);
        $balance = $totalEntries - $totalExits;
        $actualMonths = count($sortedMonths);
        $averageMonthlyBalance = $actualMonths > 0 ? round($balance / $actualMonths, 2) : 0;

        $startDate = $sortedMonths->isNotEmpty()
            ? Carbon::parse($sortedMonths->first().'-01')->startOfMonth()->format('Y-m-d')
            : null;
        $endDate = $sortedMonths->isNotEmpty()
            ? Carbon::parse($sortedMonths->last().'-01')->endOfMonth()->format('Y-m-d')
            : null;

        return new EntriesVsExitsData(
            months: $actualMonths,
            startDate: $startDate ?? '',
            endDate: $endDate ?? '',
            categories: $categories,
            entries: $entries,
            exits: $exits,
            totalEntries: $totalEntries,
            totalExits: $totalExits,
            balance: $balance,
            averageMonthlyBalance: $averageMonthlyBalance
        );
    }
}
