<?php

namespace App\Domain\Dashboard\Actions;

use App\Domain\Dashboard\DataTransferObjects\MemberEngagementData;
use App\Domain\Dashboard\Interfaces\DashboardRepositoryInterface;
use Carbon\Carbon;

class GetMemberEngagementAction
{
    public function __construct(
        private DashboardRepositoryInterface $repository
    ) {}

    public function execute(?string $month = null): MemberEngagementData
    {
        // Usar mês atual se não especificado
        $currentMonth = $month ?? Carbon::now()->format('Y-m');

        // Calcular mês anterior
        $previousMonth = Carbon::parse($currentMonth.'-01')->subMonth()->format('Y-m');

        // Dados do mês atual
        $totalMembers = $this->repository->getTotalActiveMembersByMonth($currentMonth);
        $activeTithers = $this->repository->getActiveTithersByMonth($currentMonth);
        $totalContributions = $this->repository->getTotalContributionsByMonth($currentMonth);

        $engagementRate = $totalMembers > 0 ? round($activeTithers / $totalMembers, 2) : 0;
        $averageContributionsPerMember = $totalMembers > 0
            ? round($totalContributions / $totalMembers, 2)
            : 0;

        // Dados do mês anterior
        $previousTotalMembers = $this->repository->getTotalActiveMembersByMonth($previousMonth);
        $previousActiveTithers = $this->repository->getActiveTithersByMonth($previousMonth);
        $previousTotalContributions = $this->repository->getTotalContributionsByMonth($previousMonth);

        $previousEngagementRate = $previousTotalMembers > 0
            ? round($previousActiveTithers / $previousTotalMembers, 2)
            : 0;
        $previousAverageContributionsPerMember = $previousTotalMembers > 0
            ? round($previousTotalContributions / $previousTotalMembers, 2)
            : 0;

        // Calcular tendências (crescimento percentual)
        $membersGrowth = $previousTotalMembers > 0
            ? round(($totalMembers - $previousTotalMembers) / $previousTotalMembers, 4)
            : 0;

        $tithersGrowth = $previousActiveTithers > 0
            ? round(($activeTithers - $previousActiveTithers) / $previousActiveTithers, 4)
            : 0;

        $engagementGrowth = $previousEngagementRate > 0
            ? round(($engagementRate - $previousEngagementRate) / $previousEngagementRate, 4)
            : 0;

        $contributionsGrowth = $previousTotalContributions > 0
            ? round(($totalContributions - $previousTotalContributions) / $previousTotalContributions, 4)
            : 0;

        return new MemberEngagementData(
            totalMembers: $totalMembers,
            activeTithers: $activeTithers,
            engagementRate: $engagementRate,
            averageContributionsPerMember: $averageContributionsPerMember,
            totalContributions: $totalContributions,
            previousTotalMembers: $previousTotalMembers,
            previousActiveTithers: $previousActiveTithers,
            previousEngagementRate: $previousEngagementRate,
            previousAverageContributionsPerMember: $previousAverageContributionsPerMember,
            previousTotalContributions: $previousTotalContributions,
            membersGrowth: $membersGrowth,
            tithersGrowth: $tithersGrowth,
            engagementGrowth: $engagementGrowth,
            contributionsGrowth: $contributionsGrowth,
            date: $currentMonth
        );
    }
}
