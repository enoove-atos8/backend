<?php

namespace Application\Api\v1\Financial\Exits\Exits\Controllers\DuplicityAnalisys;

use Application\Api\v1\Financial\Exits\Exits\Resources\DuplicityAnalisysExitsResourceCollection;
use Application\Api\v1\Financial\Exits\Exits\Resources\ReceiptsByExitsIdsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\DuplicitiesAnalisys\Actions\GetDuplicityAnalisysExitsAction;
use Domain\Financial\Exits\DuplicitiesAnalisys\Actions\GetReceiptsByExitsIdsAction;
use Domain\Financial\Exits\DuplicitiesAnalisys\Actions\SaveDuplicityAnalysisAction;
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
     * @param GetDuplicityAnalisysExitsAction $getDuplicityAnalisysExitsAction
     * @return ResponseFactory|Application|Response|DuplicityAnalisysExitsResourceCollection
     * @throws GeneralExceptions
     */
    public function getDuplicitiesExits(Request $request, GetDuplicityAnalisysExitsAction $getDuplicityAnalisysExitsAction): ResponseFactory|Application|Response | DuplicityAnalisysExitsResourceCollection
    {
        try
        {
            $date = $request->input('date');

            if(!is_null($date))
                $response = $getDuplicityAnalisysExitsAction->execute($date);
            else
                throw new GeneralExceptions('Date is required', 400);

            return new DuplicityAnalisysExitsResourceCollection($response);
        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetReceiptsByExitsIdsAction $getReceiptsByExitsIdsAction
     * @return ResponseFactory|Application|Response|ReceiptsByExitsIdsResourceCollection
     * @throws GeneralExceptions|BindingResolutionException
     */
    public function getReceiptsByExitsIds(Request $request, GetReceiptsByExitsIdsAction $getReceiptsByExitsIdsAction): ResponseFactory|Application|Response | ReceiptsByExitsIdsResourceCollection
    {
        try
        {
            $ids = explode(',', $request->input('ids'));
            $response = $getReceiptsByExitsIdsAction->execute($ids);

            return new ReceiptsByExitsIdsResourceCollection($response);
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
            $exits = $request->input('updatedExits');
            $saveDuplicityAnalysisAction->execute($exits);

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
