<?php

namespace Application\Api\v1\Financial\Entries\Entries\Controllers\DuplicityAnalisys;

use Application\Api\v1\Financial\Entries\Entries\Resources\DuplicityAnalisysEntriesResourceCollection;
use Application\Api\v1\Financial\Entries\Entries\Resources\ReceiptsByEntriesIdsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Entries\DuplicitiesAnalisys\Actions\GetDuplicityAnalisysEntriesAction;
use Domain\Financial\Entries\DuplicitiesAnalisys\Actions\GetReceiptsByEntriesIdsAction;
use Domain\Financial\Entries\DuplicitiesAnalisys\Actions\SaveDuplicityAnalysisAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Infrastructure\Exceptions\GeneralExceptions;
use Throwable;

class DuplicityAnalisysController extends Controller
{
    /**
     * @param Request $request
     * @param GetDuplicityAnalisysEntriesAction $getDuplicityAnalisysEntriesAction
     * @return ResponseFactory|Application|Response|DuplicityAnalisysEntriesResourceCollection
     * @throws GeneralExceptions
     */
    public function getDuplicitiesEntries(Request $request, GetDuplicityAnalisysEntriesAction $getDuplicityAnalisysEntriesAction): ResponseFactory|Application|Response | DuplicityAnalisysEntriesResourceCollection
    {
        try
        {
            $date = $request->input('date');

            if(!is_null($date))
                $response = $getDuplicityAnalisysEntriesAction->execute($date);
            else
                throw new GeneralExceptions('Date is required', 400);

            return new DuplicityAnalisysEntriesResourceCollection($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetReceiptsByEntriesIdsAction $getReceiptsByEntriesIdsAction
     * @return ResponseFactory|Application|Response|ReceiptsByEntriesIdsResourceCollection
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function getReceiptsByEntriesIds(Request $request, GetReceiptsByEntriesIdsAction $getReceiptsByEntriesIdsAction): ResponseFactory|Application|Response | ReceiptsByEntriesIdsResourceCollection
    {
        try
        {
            $ids = explode(',', $request->input('ids'));
            $response = $getReceiptsByEntriesIdsAction->execute($ids);

            return new ReceiptsByEntriesIdsResourceCollection($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }




    /**
     * @param Request $request
     * @param SaveDuplicityAnalysisAction $saveDuplicityAnalysisAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     */
    public function saveDuplicityAnalysis(Request $request, SaveDuplicityAnalysisAction $saveDuplicityAnalysisAction): ResponseFactory|Application|Response
    {
        try
        {
            $entries = $request->input('updatedEntries');
            $saveDuplicityAnalysisAction->execute($entries);

            return response([
                'message'   =>  'Duplicities analysis saved',
            ], 200);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
