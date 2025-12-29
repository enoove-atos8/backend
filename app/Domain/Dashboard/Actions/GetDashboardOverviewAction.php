<?php

namespace App\Domain\Dashboard\Actions;

use App\Domain\Dashboard\DataTransferObjects\DashboardOverviewData;
use App\Domain\Dashboard\DataTransferObjects\IndicatorData;
use App\Domain\Dashboard\DataTransferObjects\TrendData;
use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use Carbon\Carbon;

class GetDashboardOverviewAction
{
    private const MONTHS_PT_BR = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
    ];

    public function __construct(
        private DashboardRepositoryInterface $repository
    ) {}

    public function execute(): DashboardOverviewData
    {
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->format('Y-m');
        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');

        $month = (int) $currentDate->format('m');
        $year = (int) $currentDate->format('Y');
        $label = self::MONTHS_PT_BR[$month].'/'.$year;

        // Members - usa valor absoluto para trend
        $currentMembers = $this->repository->getActiveMembersCount();
        // Para members, não temos histórico mensal, então trend será 0
        $membersTrend = TrendData::calculate($currentMembers, $currentMembers, false);

        // Tithes (Entradas)
        $currentTithes = $this->repository->getTotalEntries($currentMonth);
        $previousTithes = $this->repository->getTotalEntries($previousMonth);
        $tithesTrend = TrendData::calculate($currentTithes, $previousTithes, true);

        // Purchases (Pagamentos, Repasses, Contribuições)
        $currentPurchases = $this->repository->getTotalPurchases($currentMonth);
        $previousPurchases = $this->repository->getTotalPurchases($previousMonth);
        $purchasesTrend = TrendData::calculate($currentPurchases, $previousPurchases, true);

        // Exits (Total Saídas)
        $currentExits = $this->repository->getTotalExits($currentMonth);
        $previousExits = $this->repository->getTotalExits($previousMonth);
        $exitsTrend = TrendData::calculate($currentExits, $previousExits, true);

        return new DashboardOverviewData(
            month: $month,
            year: $year,
            label: $label,
            members: new IndicatorData(
                total: $currentMembers,
                trend: $membersTrend
            ),
            tithes: new IndicatorData(
                total: $currentTithes,
                trend: $tithesTrend
            ),
            purchases: new IndicatorData(
                total: $currentPurchases,
                trend: $purchasesTrend
            ),
            exits: new IndicatorData(
                total: $currentExits,
                trend: $exitsTrend
            )
        );
    }
}
