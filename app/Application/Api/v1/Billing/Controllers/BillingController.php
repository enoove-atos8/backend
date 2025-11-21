<?php

namespace Application\Api\v1\Billing\Controllers;

use App\Domain\CentralDomain\Billing\Actions\GetBillingDetailsAction;
use App\Domain\CentralDomain\Plans\Actions\GetPlansAction;
use Application\Api\v1\Billing\Resources\BillingDetailsResource;
use Application\Api\v1\Billing\Resources\PlanResource;
use Application\Core\Http\Controllers\Controller;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class BillingController extends Controller
{
    public function __construct(
        private GetPlansAction $getPlansAction,
        private GetBillingDetailsAction $getBillingDetailsAction,
        private GetChurchAction $getChurchAction
    ) {
    }

    /**
     * Get all available plans
     *
     * @return AnonymousResourceCollection
     * @throws GeneralExceptions|Throwable
     */
    public function getPlans(): AnonymousResourceCollection
    {
        try {
            $plans = $this->getPlansAction->execute();

            return PlanResource::collection($plans);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Get billing details for the authenticated church
     *
     * @param Request $request
     * @return BillingDetailsResource
     * @throws GeneralExceptions|Throwable
     */
    public function getBillingDetails(Request $request): BillingDetailsResource
    {
        try {
            // Extract tenant_id from URL (e.g., iebrd.atos8.com -> iebrd)
            $tenant = explode('.', $request->getHost())[0];

            // Get church by tenant_id
            $church = $this->getChurchAction->execute($tenant);

            if (!$church) {
                throw new GeneralExceptions('Igreja não encontrada', 404);
            }

            // Get billing details using church ID
            $billingDetails = $this->getBillingDetailsAction->execute($church->id);

            return new BillingDetailsResource($billingDetails);
        } catch (GeneralExceptions $e) {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
