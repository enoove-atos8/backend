<?php

namespace App\Application\Api\v1\Financial\Entry\Controllers\General;

use App\Application\Api\v1\Financial\Entry\Requests\EntryRequest;
use App\Application\Api\v1\Financial\Entry\Requests\ReceiptEntryRequest;
use App\Application\Api\v1\Financial\Entry\Resources\DevolutionEntriesResourceCollection;
use App\Application\Api\v1\Financial\Entry\Resources\EntryConsolidatedResourceCollection;
use App\Application\Api\v1\Financial\Entry\Resources\EntryResource;
use App\Application\Api\v1\Financial\Entry\Resources\EntryResourceCollection;
use App\Domain\Financial\Entries\Consolidated\Actions\GetConsolidatedEntriesByStatusAction;
use App\Domain\Financial\Entries\Consolidated\Actions\GetQtdEntriesNoCompensateByMonthAction;
use App\Domain\Financial\Entries\Consolidated\Actions\UpdateStatusConsolidatedEntriesAction;
use App\Domain\Financial\Entries\Consolidated\Constants\ReturnMessages as ConsolidationEntriesReturnMessages;
use App\Domain\Financial\Entries\General\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\General\Actions\DeleteEntryAction;
use App\Domain\Financial\Entries\General\Actions\GetAmountByEntryTypeAction;
use App\Domain\Financial\Entries\General\Actions\GetDevolutionEntriesAction;
use App\Domain\Financial\Entries\General\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\General\Actions\GetEntriesToCompensateAction;
use App\Domain\Financial\Entries\General\Actions\GetEntryByIdAction;
use App\Domain\Financial\Entries\General\Actions\UpdateEntryAction;
use App\Domain\Financial\Entries\General\Constants\ReturnMessages;
use Application\Api\v1\Financial\Entry\Resources\AmountByEntryTypeResource;
use Application\Api\v1\Financial\Entry\Resources\EntriesToCompensateResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Util\Storage\S3\UploadFile;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class EntryController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:admin|pastor|treasury']);
    }

    /**
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
            $dates = $request->input('dates');
            $filters = $request->except(['dates','page']);
            $response = $getEntriesAction($dates, $filters);
            return new EntryResourceCollection($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param Request $request
     * @param GetConsolidatedEntriesByStatusAction $getConsolidationEntriesByStatus
     * @param GetQtdEntriesNoCompensateByMonthAction $getQtdEntriesNoCompensateByMonth
     * @return EntryConsolidatedResourceCollection
     * @throws GeneralExceptions
     */
    public function getConsolidationEntriesByStatus(
        Request                                $request,
        GetConsolidatedEntriesByStatusAction   $getConsolidationEntriesByStatus,
        GetQtdEntriesNoCompensateByMonthAction $getQtdEntriesNoCompensateByMonth): EntryConsolidatedResourceCollection
    {
        try
        {
            $consolidated = $request->input('consolidated');
            $monthsNotConsolidated = $getConsolidationEntriesByStatus(intval($consolidated), true);

            return new EntryConsolidatedResourceCollection($monthsNotConsolidated);
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
     * @param $id
     * @param DeleteEntryAction $deleteEntryAction
     * @return Application|Response|ResponseFactory
     * @throws GeneralExceptions|Throwable
     */
    public function deleteEntry($id, DeleteEntryAction $deleteEntryAction): Application|ResponseFactory|Response
    {
        try
        {
            $entryDeleted = $deleteEntryAction($id);

            if($entryDeleted)
            {
                return response([
                    'message'   =>  ReturnMessages::ENTRY_DELETED,
                ], 200);
            }

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
     * @param UpdateStatusConsolidatedEntriesAction $updateStatusConsolidationEntriesAction
     * @return Application|Response|ResponseFactory
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function updateStatusConsolidationEntries(Request $request, UpdateStatusConsolidatedEntriesAction $updateStatusConsolidationEntriesAction): Application|ResponseFactory|Response
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
     * @return AmountByEntryTypeResource
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getAmountByEntryType(Request $request, GetAmountByEntryTypeAction $getAmountByEntryTypeAction): AmountByEntryTypeResource
    {
        try
        {
            $rangeMonthlyDate = $request->input('rangeMonthlyDate');
            $amountType = $request->input('amountType');
            $entryType = $request->input('entryType');

            $response = $getAmountByEntryTypeAction($rangeMonthlyDate, $amountType, $entryType);
            return new AmountByEntryTypeResource($response);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param ReceiptEntryRequest $receiptEntryRequest
     * @param UploadFile $uploadFile
     * @return Response
     * @throws GeneralExceptions
     */
    public function uploadEntryReceipt(ReceiptEntryRequest $receiptEntryRequest, UploadFile $uploadFile): Response
    {
        try
        {
            $tenantS3PathObject = 'entries/assets/receipts';
            $tenant = explode('.', $receiptEntryRequest->getHost())[0];
            $file = $receiptEntryRequest->files->get('receipt');

            $response = $uploadFile->upload($file, $tenantS3PathObject, $tenant);

            if($response)
                return response([
                    'message'   => ReturnMessages::ENTRY_RECEIPT_PROCESSED,
                    'receipt'   =>  $response
                ], 200);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param Request $request
     * @param GetEntriesToCompensateAction $getEntriesToCompensateAction
     * @return EntriesToCompensateResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getEntriesByTransactionCompensation(Request $request, GetEntriesToCompensateAction $getEntriesToCompensateAction): EntriesToCompensateResourceCollection
    {
        try
        {
            $date = $request->input('date');
            $entries = $getEntriesToCompensateAction($date);
            return new EntriesToCompensateResourceCollection($entries->items());
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @param Request $request
     * @param GetDevolutionEntriesAction $getDevolutionEntriesAction
     * @return DevolutionEntriesResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getDevolutionEntries(Request $request, GetDevolutionEntriesAction $getDevolutionEntriesAction): DevolutionEntriesResourceCollection
    {
        try
        {
            $date = $request->input('date');
            $entries = $getDevolutionEntriesAction($date);
            return new DevolutionEntriesResourceCollection($entries);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
