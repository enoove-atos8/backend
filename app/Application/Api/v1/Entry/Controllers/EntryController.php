<?php

namespace Application\Api\v1\Entry\Controllers;

use Domain\Entries\Constants\ReturnMessages;
use Domain\ConsolidationEntries\Constants\ReturnMessages as ConsolidationEntriesReturnMessages;
use Application\Api\v1\Entry\Requests\EntryRequest;
use Application\Api\v1\Entry\Resources\EntryConsolidationResourceCollection;
use Application\Api\v1\Entry\Resources\EntryResource;
use Application\Api\v1\Entry\Resources\EntryResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\ConsolidationEntries\Actions\GetConsolidationEntriesByStatus;
use Domain\ConsolidationEntries\Actions\GetConsolidationEntriesStatusByDateAction;
use Domain\ConsolidationEntries\Actions\UpdateStatusConsolidationEntriesAction;
use Domain\Entries\Actions\CreateEntryAction;
use Domain\Entries\Actions\GetAmountByEntryTypeAction;
use Domain\Entries\Actions\GetEntriesAction;
use Domain\Entries\Actions\GetEntryByIdAction;
use Domain\Entries\Actions\UpdateEntryAction;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @return Application|ResponseFactory|Response
     * @throws TenantCouldNotBeIdentifiedById
     * @throws UnknownProperties
     * @throws Throwable
     */
    public function createEntry(EntryRequest $entryRequest, CreateEntryAction $createEntryAction): Application|ResponseFactory|Response
    {
        try
        {
            $createEntryAction($entryRequest->entryData(), $entryRequest->consolidationEntriesData());

            return response([
                'message'   =>  ReturnMessages::SUCCESS_ENTRY_REGISTERED,
            ], 201);
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetEntriesAction $getEntriesAction
     * @return EntryResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getEntriesByMonthlyRange(Request $request, GetEntriesAction $getEntriesAction): EntryResourceCollection
    {
        try
        {
            $response = $getEntriesAction($request);
            return new EntryResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetConsolidationEntriesByStatus $getConsolidationEntriesByStatus
     * @return EntryConsolidationResourceCollection
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function getConsolidationEntriesByStatus(Request $request, GetConsolidationEntriesByStatus $getConsolidationEntriesByStatus): EntryConsolidationResourceCollection
    {
        try
        {
            $status = $request->input('status');
            $response = $getConsolidationEntriesByStatus(intval($status));
            return new EntryConsolidationResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param $id
     * @param GetEntryByIdAction $getEntryByIdAction
     * @return EntryResource
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getEntryById($id, GetEntryByIdAction $getEntryByIdAction): EntryResource
    {
        try
        {
            $response = $getEntryByIdAction($id);
            return new EntryResource($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     *
     * Store a newly created resource in storage.
     *
     * @param EntryRequest $entryRequest
     * @param $id
     * @param UpdateEntryAction $updateEntryAction
     * @return Application|Response|ResponseFactory
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function updateEntry(EntryRequest $entryRequest, $id, UpdateEntryAction $updateEntryAction): Application|ResponseFactory|Response
    {
        try
        {
            $updateEntryAction($id, $entryRequest->entryData(), $entryRequest->consolidationEntriesData());

            return response([
                'message'   =>  ReturnMessages::INFO_UPDATED_ENTRY,
            ], 200);

        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     *
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param UpdateStatusConsolidationEntriesAction $updateStatusConsolidationEntriesAction
     * @return Application|Response|ResponseFactory
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function updateStatusConsolidationEntries(Request $request, UpdateStatusConsolidationEntriesAction $updateStatusConsolidationEntriesAction): Application|ResponseFactory|Response
    {
        try
        {
            $response = $updateStatusConsolidationEntriesAction($request->input('date'), $request->input('status'));

            if($response)
            {
                return response([
                    'message'   =>  ConsolidationEntriesReturnMessages::SUCCESS_ENTRIES_CONSOLIDATED,
                ], 200);
            }
        }
        catch(GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetAmountByEntryTypeAction $getAmountByEntryTypeAction
     * @return array
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getAmountByEntryType(Request $request, GetAmountByEntryTypeAction $getAmountByEntryTypeAction): array
    {
        try
        {
            $rangeMonthlyDate = $request->input('rangeMonthlyDate');
            $amountType = $request->input('amountType');
            $entryType = $request->input('entryType');

            $response = $getAmountByEntryTypeAction($rangeMonthlyDate, $amountType, $entryType);
            return [
                'total'         => $response,
                'amountType'    => $amountType,
                'entryType'     => $entryType,
            ];

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
