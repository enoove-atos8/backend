<?php

namespace Application\Api\v1\Financial\Entries\Entries\Controllers\General;

use App\Domain\Financial\Entries\Consolidation\Actions\GetConsolidatedEntriesByStatusAction;
use App\Domain\Financial\Entries\Consolidation\Actions\GetQtdEntriesNoCompensateByMonthAction;
use App\Domain\Financial\Entries\Consolidation\Actions\UpdateStatusConsolidatedEntriesAction;
use App\Domain\Financial\Entries\Consolidation\Constants\ReturnMessages as ConsolidationEntriesReturnMessages;
use App\Domain\Financial\Entries\Entries\Actions\CreateEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\DeleteEntryAction;
use App\Domain\Financial\Entries\Entries\Actions\GetAmountByEntryTypeAction;
use App\Domain\Financial\Entries\Entries\Actions\GetDevolutionEntriesAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntriesAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntriesToCompensateAction;
use App\Domain\Financial\Entries\Entries\Actions\GetEntryByIdAction;
use App\Domain\Financial\Entries\Entries\Actions\UpdateEntryAction;
use App\Domain\Financial\Entries\Entries\Constants\ReturnMessages;
use Application\Api\v1\Financial\Entries\Entries\Requests\EntryRequest;
use Application\Api\v1\Financial\Entries\Entries\Requests\ReceiptEntryRequest;
use Application\Api\v1\Financial\Entries\Entries\Resources\AmountByEntryTypeResource;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntryConsolidatedResourceCollection;
use Application\Api\v1\Financial\Entries\Entries\Resources\DevolutionEntriesResourceCollection;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntriesToCompensateResourceCollection;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntryResource;
use Application\Api\v1\Financial\Entries\Entries\Resources\EntryResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Ecclesiastical\Groups\Actions\GetAllGroupsAction;
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

class EntriesController extends Controller
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
            $createEntryAction->execute($entryRequest->entryData(), $entryRequest->consolidationEntriesData());

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
     * @param GetAllGroupsAction $getAllGroupsAction
     * @return EntryResourceCollection
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getEntriesByMonthlyRange(Request $request, GetEntriesAction $getEntriesAction, GetAllGroupsAction $getAllGroupsAction): EntryResourceCollection
    {
        try
        {
            $dates = $request->input('dates');
            $filters = $request->except(['dates','page']);
            $entries = $getEntriesAction->execute($dates, $filters);
            $groups = $getAllGroupsAction->execute();
            return new EntryResourceCollection($entries, $groups);

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
            $response = $getEntryByIdAction->execute($id);
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
            $updateEntryAction->execute($id, $entryRequest->entryData(), $entryRequest->consolidationEntriesData());

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
            $entryDeleted = $deleteEntryAction->execute($id);

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
     * @param Request $request
     * @param GetAmountByEntryTypeAction $getAmountByEntryTypeAction
     * @return ResponseFactory|\Illuminate\Foundation\Application|Response|AmountByEntryTypeResource
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getAmountByEntryType(Request $request, GetAmountByEntryTypeAction $getAmountByEntryTypeAction): ResponseFactory|\Illuminate\Foundation\Application|Response | AmountByEntryTypeResource
    {
        try
        {
            $rangeMonthlyDate = $request->input('rangeMonthlyDate');
            $entryType = $request->input('entryType');

            $response = $getAmountByEntryTypeAction->execute($rangeMonthlyDate, $entryType);

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
            $entries = $getEntriesToCompensateAction->execute($date);
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
            $entries = $getDevolutionEntriesAction->execute($date);
            return new DevolutionEntriesResourceCollection($entries);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
