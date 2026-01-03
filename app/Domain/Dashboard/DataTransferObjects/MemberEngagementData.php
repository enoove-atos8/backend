<?php

namespace App\Domain\Dashboard\DataTransferObjects;

class MemberEngagementData
{
    public function __construct(
        public int $totalMembers,
        public int $activeTithers,
        public float $engagementRate,
        public float $averageContributionsPerMember,
        public int $totalContributions,
        public int $previousTotalMembers,
        public int $previousActiveTithers,
        public float $previousEngagementRate,
        public float $previousAverageContributionsPerMember,
        public int $previousTotalContributions,
        public float $membersGrowth,
        public float $tithersGrowth,
        public float $engagementGrowth,
        public float $contributionsGrowth,
        public string $date
    ) {}
}
