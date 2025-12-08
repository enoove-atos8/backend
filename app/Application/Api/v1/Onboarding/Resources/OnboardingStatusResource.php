<?php

namespace App\Application\Api\v1\Onboarding\Resources;

use App\Domain\Onboarding\DataTransferObjects\OnboardingStatusData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OnboardingStatusResource extends JsonResource
{
    /**
     * @var OnboardingStatusData
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'onboardingRequired' => ! $this->resource->completed,
            'completed' => $this->resource->completed,
            'currentStep' => $this->resource->currentStep,
            'totalSteps' => $this->resource->totalSteps,
            'totalRequiredSteps' => $this->resource->totalRequiredSteps,
            'completedSteps' => $this->resource->completedSteps,
            'progressPercentage' => $this->resource->progressPercentage,
            'steps' => $this->formatSteps($this->resource->steps),
            'pendingSteps' => $this->formatPendingSteps($this->resource->pendingSteps),
        ];
    }

    private function formatSteps(array $steps): array
    {
        return array_map(fn ($step) => [
            'step' => $step->step,
            'name' => $step->name,
            'title' => $step->title,
            'description' => $step->description,
            'completed' => $step->completed,
            'required' => $step->required,
            'count' => $step->count,
            'minimumRequired' => $step->minimumRequired,
            'skippable' => $step->skippable,
            'details' => $step->details,
        ], $steps);
    }

    private function formatPendingSteps(array $pendingSteps): array
    {
        return array_map(fn ($step) => [
            'step' => $step->step,
            'name' => $step->name,
            'title' => $step->title,
            'description' => $step->description,
            'required' => $step->required,
        ], $pendingSteps);
    }
}
