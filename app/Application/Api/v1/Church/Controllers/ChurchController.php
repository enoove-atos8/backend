<?php

namespace Application\Api\v1\Church\Controllers;

use Application\Api\v1\Church\Requests\ChurchRequest;
use Application\Api\v1\Church\Resources\ChurchResource;
use Application\Core\Http\Controllers\Controller;
use Domain\CentralDomain\Churches\Church\Actions\CreateChurchAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ChurchController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param ChurchRequest $churchRequest
     * @param CreateChurchAction $createChurchAction
     * @return ChurchResource
     * @throws TenantCouldNotBeIdentifiedById
     * @throws UnknownProperties
     * @throws Throwable
     */
    public function createChurch(ChurchRequest $churchRequest, CreateChurchAction $createChurchAction): ChurchResource
    {
        try {
            $response = $createChurchAction->execute(
                $churchRequest->churchData(),
                $churchRequest->userData(),
                $churchRequest->userDetailData());

            return new ChurchResource($response);

        }catch(GeneralExceptions $e){
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
