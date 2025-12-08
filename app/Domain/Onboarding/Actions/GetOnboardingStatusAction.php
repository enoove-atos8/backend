<?php

namespace App\Domain\Onboarding\Actions;

use App\Domain\Onboarding\DataTransferObjects\OnboardingStatusData;
use App\Domain\Onboarding\Interfaces\OnboardingRepositoryInterface;

class GetOnboardingStatusAction
{
    public function __construct(
        private OnboardingRepositoryInterface $onboardingRepository
    ) {}

    public function execute(): OnboardingStatusData
    {
        return $this->onboardingRepository->getOnboardingStatus();
    }
}
