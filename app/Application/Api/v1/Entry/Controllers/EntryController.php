<?php

namespace Application\Api\v1\Entry\Controllers;

use Application\Api\v1\Entry\Requests\EntryRequest;
use Application\Api\v1\Entry\Resources\EntryResource;
use Application\Api\v1\Entry\Resources\EntryResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Entries\Actions\CreateEntryAction;
use Domain\Entries\Actions\GetAmountByEntryTypeAction;
use Domain\Entries\Actions\GetEntriesAction;
use Domain\Entries\Actions\GetEntryByIdAction;
use Domain\Entries\Actions\UpdateEntryAction;
use Exception;
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
        try {
            $response = $createEntryAction($entryRequest->entryData());
            return response([
                'id'        =>  $response->id,
                'message'   =>  'Entrada cadastrada com sucesso!',
            ], 201);

        }catch(Exception $e){
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

        }catch (Exception $e){
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }



    /**
     * @throws GeneralExceptions|Throwable
     */
    public function getEntryById($id, GetEntryByIdAction $getEntryByIdAction): EntryResource
    {
        try{
            $response = $getEntryByIdAction($id);
            return new EntryResource($response);

        }catch (Exception $e){
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
     * @return Application|ResponseFactory|Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function updateEntry(EntryRequest $entryRequest, $id, UpdateEntryAction $updateEntryAction): Application|ResponseFactory|Response
    {
        try {
            $result = null;
            $response = $updateEntryAction($id, $entryRequest->entryData());

            if($response)
                $result = response([
                    'message'   =>  'Entrada atualizada com sucesso!',
                ], 201);

            return $result;

        }catch(Exception $e){
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
        try{
            $rangeMonthlyDate = $request->input('rangeMonthlyDate');
            $amountType = $request->input('amountType');
            $entryType = $request->input('entryType');

            $response = $getAmountByEntryTypeAction($rangeMonthlyDate, $amountType, $entryType);
            return [
                'total'       => $response,
                'amountType'  => $amountType,
                'entryType'  => $entryType,
            ];

        }catch (Exception $e){
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
