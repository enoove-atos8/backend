<?php

namespace App\Application\Api\v1\Dashboard\Resources;

use App\Domain\Dashboard\DataTransferObjects\MemberEngagementData;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberEngagementResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        /** @var MemberEngagementData $data */
        $data = $this->resource;

        return [
            'success' => true,
            'data' => [
                'currentMonth' => [
                    'totalMembers' => $data->totalMembers,
                    'activeTithers' => $data->activeTithers,
                    'engagementRate' => round($data->engagementRate, 2),
                    'averageContributionsPerMember' => round($data->averageContributionsPerMember, 2),
                    'totalContributions' => $data->totalContributions,
                ],
                'previousMonth' => [
                    'totalMembers' => $data->previousTotalMembers,
                    'activeTithers' => $data->previousActiveTithers,
                    'engagementRate' => round($data->previousEngagementRate, 2),
                    'averageContributionsPerMember' => round($data->previousAverageContributionsPerMember, 2),
                    'totalContributions' => $data->previousTotalContributions,
                ],
                'trends' => [
                    'membersGrowth' => round($data->membersGrowth, 4),
                    'tithersGrowth' => round($data->tithersGrowth, 4),
                    'engagementGrowth' => round($data->engagementGrowth, 4),
                    'contributionsGrowth' => round($data->contributionsGrowth, 4),
                ],
                'date' => $data->date,
            ],
        ];
    }
}
