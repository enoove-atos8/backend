<?php

namespace App\Domain\Onboarding\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class OnboardingStatusData extends DataTransferObject
{
    public bool $completed;

    public int $currentStep;

    public int $totalSteps;

    public int $totalRequiredSteps;

    public int $completedSteps;

    public int $progressPercentage;

    /** @var OnboardingStepData[] */
    public array $steps;

    /** @var OnboardingStepData[] */
    public array $pendingSteps;

    public static function fromResponse(
        bool $completed,
        int $currentStep,
        int $totalSteps,
        int $totalRequiredSteps,
        int $completedSteps,
        int $progressPercentage,
        array $steps,
        array $pendingSteps
    ): self {
        return new self(
            completed: $completed,
            currentStep: $currentStep,
            totalSteps: $totalSteps,
            totalRequiredSteps: $totalRequiredSteps,
            completedSteps: $completedSteps,
            progressPercentage: $progressPercentage,
            steps: $steps,
            pendingSteps: $pendingSteps,
        );
    }
}
