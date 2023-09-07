<?php

namespace Application\Api\v1\Entry\Controllers;

use App\Domain\Entries\Actions\GetEntriesAction;
use Application\Api\v1\Entry\Requests\EntryRequest;
use Application\Api\v1\Entry\Resources\EntryResource;
use Application\Api\v1\Entry\Resources\EntryResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Entries\Actions\CreateEntryAction;
use Illuminate\Http\Request;
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


    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getEntriesByMonthlyRange(Request $request, GetEntriesAction $getEntriesAction): EntryResourceCollection
    {
        try{
            $response = $getEntriesAction($request);
            return new EntryResourceCollection($response);

        }catch (\Exception $e){
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
