<?php

namespace Application\Core\Http\Middleware;

use App\Application\Api\v1\Onboarding\Resources\OnboardingStatusResource;
use App\Domain\Onboarding\Actions\GetOnboardingStatusAction;
use Closure;
use Illuminate\Http\Request;

class CheckOnboardingMiddleware
{
    public function __construct(
        private GetOnboardingStatusAction $getOnboardingStatusAction
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $onboardingStatus = $this->getOnboardingStatusAction->execute();

        if (! $onboardingStatus->completed) {
            return (new OnboardingStatusResource($onboardingStatus))
                ->response()
                ->setStatusCode(200);
        }

        return $next($request);
    }
}
