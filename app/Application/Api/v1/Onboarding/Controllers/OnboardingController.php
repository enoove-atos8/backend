<?php

namespace App\Application\Api\v1\Onboarding\Controllers;

use App\Application\Api\v1\Onboarding\Resources\OnboardingStatusResource;
use App\Domain\Onboarding\Actions\GetOnboardingStatusAction;
use Application\Core\Http\Controllers\Controller;

class OnboardingController extends Controller
{
    public function __construct(
        private GetOnboardingStatusAction $getOnboardingStatusAction
    ) {}

    public function getStatus(): OnboardingStatusResource
    {
        $status = $this->getOnboardingStatusAction->execute();

        return new OnboardingStatusResource($status);
    }
}
