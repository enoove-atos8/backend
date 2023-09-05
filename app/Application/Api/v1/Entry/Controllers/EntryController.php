<?php

namespace Application\Api\v1\Entry\Controllers;

use Application\Api\v1\Entry\Requests\EntryRequest;
use Application\Api\v1\Entry\Resources\EntryResource;
use Application\Core\Http\Controllers\Controller;
use Domain\Entries\Actions\CreateEntryAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class EntryController extends Controller
{
    /**
     *
     * Store a newly created resource in storage.
     *
     * @param EntryRequest $entryRequest
     * @param CreateEntryAction $createEntryAction
     * @return EntryResource
     * @throws TenantCouldNotBeIdentifiedById
     * @throws UnknownProperties
     * @throws Throwable
     */
    public function createEntry(EntryRequest $entryRequest, CreateEntryAction $createEntryAction): EntryResource
    {
        try {
            $response = $createEntryAction($entryRequest->entryData());
            return new EntryResource($response);

        }catch(\Exception $e){
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
